<?php

namespace App\Filament\Exports;

use App\Models\DistributionList;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class DistributionListExporter extends Exporter
{
    protected static ?string $model = DistributionList::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('member.staff_id')->label('Staff ID'),
            ExportColumn::make('member.name')->label('Name'),
            ExportColumn::make('distributionItem.name')->label('Item'),
            ExportColumn::make('distribution.description'),
            ExportColumn::make('quantity'),
            ExportColumn::make('user.name')->label('Issued By'),
            ExportColumn::make('created_at')->label('Issued Date'),
            ExportColumn::make('is_received')->formatStateUsing(function($state){
                return $state ? 'Received' : 'Not Received';
            }),
            ExportColumn::make('received_date'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your distribution list export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}