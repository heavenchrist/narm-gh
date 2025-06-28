<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\MemberContribution;
use Illuminate\Support\Collection;
use DateTime;
use Filament\Notifications\Notification;

class MemberImportService
{
        protected array $failedRows = [];

       public function import(array $rows): array
{
    $failed = [];

    DB::beginTransaction();

    try {
        foreach ($rows as $row) {
            try {
                // Skip if critical fields are missing
                $missing = [];
                    if (empty($row['name_of_employee'])) {
                        $missing[] = 'NAME OF EMPLOYEE';
                    }
                    if (empty($row['employee_no'])) {
                        $missing[] = 'EMPLOYEE NO';
                    }
                    if (empty($row['period'])) {
                        $missing[] = 'PERIOD';
                    }
                    if (empty($row['amount'])) {
                        $missing[] = 'AMOUNT';
                    }
                    if (empty($row['region'])) {
                        $missing[] = 'REGION';
                    }

                    if (!empty($missing)) {
                        $failed[] = $row + [
                            '_error' => 'Missing required fields: ' . implode(', ', $missing)
                        ];
                        continue;
                    }

                // Find or create user
                $member = User::where('staff_id', $row['employee_no'])->first();

                if (!$member) {
                    $member = User::create([
                        'name' => $row['name_of_employee'] ?? 'Unnamed',
                        'email' => $row['employee_no'] . '@narmghana.org',
                        'password' => bcrypt($row['employee_no']),
                        'staff_id' => $row['employee_no'],
                        'place_of_work' => $row['management_unit'] ?? '',
                        'region_id' => $row['region'],
                        'district' => $row['district'] ?? '',
                    ]);
                } else {
                    $member->update([
                        'place_of_work' => $row['management_unit'] ?? '',
                        'region_id' => $row['region'],
                        'district' => $row['district'] ?? '',
                    ]);
                }

                // Parse the period
                $newDate = new \DateTime($row['period']);
                $period = $newDate->format('Y-m-t');

                // Save or update the contribution
                $member->memberContributions()->updateOrCreate(
                    [
                        'member_id' => $member->id,
                        'staff_id' => $member->staff_id,
                        'period' => $period,
                    ],
                    [
                        'amount' => $row['amount'],
                    ]
                );

            } catch (\Throwable $rowError) {
                $failed[] = $row + ['_error' => $rowError->getMessage()];
                continue; // Don't let one bad row stop the rest
            }
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();

        // Add a generic failure message for all rows if outer exception fails
        foreach ($rows as $row) {
            $failed[] = $row + ['_error' => $e->getMessage()];
        }
    }

    return $failed;
}

       public function import1(array $rows, int $chunkSize = 100): void
{
    try {
        $chunks = collect($rows)->chunk($chunkSize);

        foreach ($chunks as $chunkIndex => $chunk) {
            DB::beginTransaction();

            foreach ($chunk as $index => $row) {
                try {
                    if (
                        empty($row['employee_no']) ||
                        empty($row['name_of_employee']) ||
                        empty($row['period']) ||
                        empty($row['region']) ||
                        empty($row['amount'])
                    ) {
                        $this->logFailure($index, 'Missing required fields.', $row);
                        continue;
                    }

                    $member = User::firstOrCreate(
                        ['staff_id' => $row['employee_no']],
                        [
                            'name'     => $row['name_of_employee'],
                            'email'    => $row['name_of_employee'] . "@narmghana.org",
                            'password' => bcrypt($row['employee_no']),
                        ]
                    );

                    $member->update([
                        'place_of_work' => $row['management_unit'] ?? null,
                        'region_id'     => $row['region'] ?? null,
                        'district'      => $row['district'] ?? null,
                    ]);

                    $period = (new DateTime($row['period']))->format('Y-m-t');

                    $member->memberContributions()->updateOrCreate(
                        [
                            'member_id' => $member->id,
                            'staff_id'  => $member->staff_id,
                            'period'    => $period,
                        ],
                        [
                            'amount' => $row['amount'],
                        ]
                    );

                } catch (\Throwable $e) {
                    $this->logFailure($index, $e->getMessage(), $row);
                    continue;
                }
            }

            DB::commit();
        }

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::critical('Import failed during chunk: ' . $e->getMessage());
        Notification::make()
            ->danger()
            ->title('Critical Import Failure')
            ->body('Import aborted due to a system error.')
            ->persistent()
            ->send();
        return;
    }

    $this->notifyResult();
}

        protected function logFailure(int $index, string $reason, array $data): void
        {
                Log::error("Row $index failed: $reason");
                    $this->failedRows[] = [
                    'row_index' => $index,
                    'reason' => $reason,
                    'data' => $data,
                    ];
        }

        protected function notifyResult(): void
        {
            if (count($this->failedRows)) {
                $url = $this->exportFailedRowsAsCsv();
                Notification::make()
                    ->title('Import Completed with Errors')
                    ->danger()
                    ->body("Some rows failed to import. [Download Errors]($url)")
                    ->persistent()
                    ->send();
            } else
            {
                Notification::make()
                ->title('Import Successful')
                ->success()
                ->body('All rows imported successfully.')
                ->send();
            }
        }

        protected function exportFailedRowsAsCsv(): string
        {
                $filename = 'temp/import_errors_' . now()->timestamp . '.csv';
                $csv = fopen('php://temp', 'r+');

                fputcsv($csv, ['Row Index', 'Reason', 'Data']);

                foreach ($this->failedRows as $row) {
                    fputcsv($csv, [
                    $row['row_index'],
                    $row['reason'],
                    json_encode($row['data']),
                ]);
             }

                rewind($csv);
                Storage::put($filename, stream_get_contents($csv));
                fclose($csv);

                return Storage::url($filename);
        }
}
