<?php

namespace App\Filament\Resources\ContributionResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Region;
use Illuminate\Support\Str;
use App\Models\Contribution;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\File;
use App\Services\MemberImportService;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Response;
use App\Traits\HasSampleCsvDownloadTrait;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Imports\ContributionImporter;
use App\Filament\Resources\ContributionResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Forms\Components\Actions\Action as FormAction;
use App\Filament\Resources\ContributionResource\Widgets\ContributionStatsOverview;

class ManageContributions extends ManageRecords
{
    use ExposesTableToWidgets;
     use HasSampleCsvDownloadTrait;

    protected static string $resource = ContributionResource::class;

    protected function getHeaderActions(): array
    {
        $headers = ['EMPLOYEE No','NAME OF EMPLOYEE','MANAGEMENT UNIT','DISTRICT','REGION','AMOUNT','PERIOD',];
        $sampleCsvUrl = $this->generateSampleCsv($headers,'contributions');
        return [
             Action::make('Contribution')->label('Import Contributions')
                    ->modalHeading('Import Contributions')
                    ->icon('heroicon-o-arrow-up-on-square-stack')
                    ->modalDescription('Upload Members Contributions with csv format')
                    ->closeModalByClickingAway(false)
                    ->modalSubmitActionLabel('Import')
                    ->modalDescription(
                            new HtmlString('<a href="' . $sampleCsvUrl . '" target="_blank" class="text-primary-600 underline">Download Sample CSV</a>')
                            )

                    ->modalWidth(MaxWidth::Medium)->form([
                        FileUpload::make('contributions')
                            ->hiddenLabel()
                            ->required()
                            ->acceptedFileTypes(['text/csv'])
,
                    ])->action(function($data){

                $filePath = storage_path('app/public/' . $data['contributions']);

                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body('The uploaded file was not found.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $regions = Region::all()->pluck('name', 'id')->toArray();
                    $regions = array_map(function ($value) {
                        return str_replace(' region', '', strtolower($value));
                    }, $regions);
                    $expectedHeaders = [
                        'employee_no',
                        'name_of_employee',
                        'management_unit',
                        'district',
                        'region',
                        'amount',
                        'period',
                    ];
                    $rows = array_map('str_getcsv', file($filePath));
                    $headers = array_shift($rows);
                    $headers = array_map(fn($h) => str_replace(' ', '_', strtolower($h)), $headers);

                    // Check for missing headers
                    $missingHeaders = array_diff($expectedHeaders, $headers);

                    if (!empty($missingHeaders)) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body(new HtmlString('The following required headers are missing: <b>' . implode(', ', $missingHeaders).'</b>'))
                            ->danger()
                            ->duration(5000)
                            ->send();

                        return; // Halt further processing
                    }

                    $parsed = array_map(function ($row) use ($headers, $regions) {
                        $data = array_combine($headers, $row);
                        $data['amount'] = (float) $data['amount'];
                        $data['period'] = preg_replace('/\s+/', ' ', $data['period']);
                        $data['employee_no'] = preg_replace('/\s+/', ' ', $data['employee_no']);
                        $regionName = str_replace(' region', '', strtolower($data['region']));
                        $data['region'] = array_search($regionName, $regions);
                        return $data;
                    }, $rows);

                    $importService = new MemberImportService();

                    // Chunk and collect failures
                    $chunkSize = 100;
                   $allFailed = [];

                        foreach (array_chunk($parsed, $chunkSize) as $chunk) {
                            $failed = $importService->import($chunk) ?? []; // default to empty array if null
                            $allFailed = array_merge($allFailed, $failed);
                        }

                        //failed
                   if (!empty($allFailed)) {
    $filename = 'failed_import_' . Str::uuid() . '.csv';

    // Use public disk and correct path
    Storage::disk('public')->makeDirectory('failed_imports');

    $csvPath = storage_path('app/public/failed_imports/' . $filename);

    $fp = fopen($csvPath, 'w');
    fputcsv($fp, array_keys($allFailed[0]));

    foreach ($allFailed as $fail) {
        fputcsv($fp, $fail);
    }

    fclose($fp);

    // Get the public URL for download
    $url = Storage::url('failed_imports/' . $filename); // results in /storage/failed_imports/...

   Notification::make()
    ->title('Import completed with some errors')
    ->body(new HtmlString("Some rows failed to import. <a href=\"$url\" target=\"_blank\" class=\"underline text-primary-600\">Download CSV</a>"))
    ->danger()
    ->persistent()
    ->send();
         // Download the CSV file
        //return Response::download($csvPath);
            //->deleteFileAfterSend(true);
} else {
                        Notification::make()
                            ->title('Import Successful')
                            ->body('Contributions uploaded Successfully')
                            ->success()
                            ->send();
                /* $regions = Region::all()->pluck('name','id')->toArray();
                //remove the word region from the region name
                $regions = array_map(function($value){
                    return str_replace(' region','',strtolower($value));
                },$regions);
               // dd(array_search('western',$regions));
                $file = public_path('storage/'.$data['contributions']);
                $contrinutions = array_map('str_getcsv',file($file));

                ini_set('max_execution_time', '0');
                //get the first row as headers
                $headers = array_shift($contrinutions);
                $headers = array_map(function ($header) {
                            return str_replace(' ', '_', strtolower($header));
                        }, $headers);
               // dd($headers,$contrinutions);
                $results = array_map(function ($row) use ($headers,$regions) {
                    $data = array_combine($headers, $row);

                    $data['amount'] = (float) $data['amount'];
                    $data['period'] = preg_replace('/\s+/', ' ', $data['period']);
                    $data['employee_no'] = preg_replace('/\s+/', ' ', $data['employee_no']);
                    $region = str_replace(' region','',strtolower($data['region']));
                    $region_id = array_search($region,$regions);
                    $data['region'] = $region_id;

                    return $data;

                }, $contrinutions);
                $importService = new MemberImportService();
                    $importService->import($results); // $results = your parsed spreadsheet data */

                /* foreach($results as $row){
                    $member = User::where('staff_id',$row['employee_no'])->first();

                        if(!$member){
                            $member = User::create([
                                    'name'=>$row['name_of_employee'],
                                    'email'=>$row['name_of_employee']."@narmghana.org",
                                    'password'=> bcrypt($row['employee_no']),
                                    'staff_id'=>$row['employee_no'],
                                    'place_of_work'=>$row['management_unit'],
                                    'region_id'=>$row['region'],
                                    'district'=>$row['district'],
                                ]);
                        }else{
                            $member->place_of_work = $row['management_unit'];
                            $member->region_id = $row['region'];
                            $member->district = $row['district'];
                            $member->update();
                        }
                        $newDate = new \DateTime($row['period']);
                        $period = $newDate->format('Y-m-t');
                        $member->memberContributions()->updateOrCreate([
                            'member_id' => $member->id,
                            'staff_id' => $member->staff_id,
                            'period' => $period,
                        ],[
                            'amount' => $row['amount']
                        ]);
                        */
                }



            }),
        ];
    }

    //add widget
    protected function getHeaderWidgets(): array
    {
        return [
            // Add your widgets here
            ContributionStatsOverview::class,
        ];
    }
}
