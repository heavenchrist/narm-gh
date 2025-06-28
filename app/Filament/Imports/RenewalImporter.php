<?php

namespace App\Filament\Imports;

use App\Models\Renewal;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class RenewalImporter extends Importer
{
    protected static ?string $model = Renewal::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('staff_id')
                ->requiredMapping()->guess(['staffid'])
                ->rules(['required']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('pin_ain')->guess(['pin','ain'])
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('registration_number')->guess(['reg_number', 'RegNo','regn'])
                ->requiredMapping(),
            ImportColumn::make('expiry_date')->guess(['valid_until','valid'])
                ->rules(['date']),
            ImportColumn::make('renewal_date')->guess(['renewed_date','created'])
                ->rules(['date']),
            ImportColumn::make('period')
                ->requiredMapping()
                ->rules(['required', 'max:4']),

           /*  ImportColumn::make('telephone')
            ->requiredMapping()
            ->rules(['required', 'max:4']), */
        ];
    }

    public function resolveRecord(): ?Renewal
    {
        return Renewal::firstOrNew([
          //  return Renewal::updateOrCreate([
        //     // Update existing records, matching them by `$this->data['column_name']`
                'staff_id' => $this->data['staff_id'],
                'period' => $this->data['period'],
                'pin_ain' => $this->data['pin_ain'],

         ]/* ,[
            'registration_number' => $this->data['registration_number'],
                'renewal_date' => $this->data['renewal_date'],
                'expiry_date' => $this->data['expiry_date'],
                'name' => $this->data['name'],
                //'telephone' => $this->data['telephone'],
         ] */);

       // return new Renewal();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your renewal import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
