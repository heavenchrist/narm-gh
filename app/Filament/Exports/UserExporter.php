<?php

namespace App\Filament\Exports;

use App\Enums\Gender;
use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('staff_id'),
            ExportColumn::make('email'),
            ExportColumn::make('date_of_birth'),
            ExportColumn::make('place_of_birth'),
            ExportColumn::make('residential_address'),
            ExportColumn::make('telephone'),
            ExportColumn::make('pin_number'),
            ExportColumn::make('registration_number'),
            ExportColumn::make('place_of_work'),
            ExportColumn::make('rank.name'),
            ExportColumn::make('region.name'),
            ExportColumn::make('district'),
            ExportColumn::make('gender')
                ->getStateUsing(function ( $record)  {
                             return $record->gender?->value;
             }),
            ExportColumn::make('marital_status') ->getStateUsing(function ( $record)  {
                             return $record->marital_status?->value;
                            }),
            ExportColumn::make('next_of_kin'),
            ExportColumn::make('next_of_kin_contact'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your membership export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}