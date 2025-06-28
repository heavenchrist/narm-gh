<?php

namespace App\Filament\Portal\Resources\MyContributionResource\Pages;

use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Exports\MyContributionExporter;
use App\Filament\Portal\Resources\MyContributionResource;

class ManageMyContributions extends ManageRecords
{
    protected static string $resource = MyContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
           ExportAction::make()
                ->label('Export Contribution')
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->exporter(MyContributionExporter::class)
                ->columnMapping(false)
                ->chunkSize(100)
                ->maxRows(1000)
                ->fileName(fn (Export $export): string => "contributions-{$export->getKey()}.csv"),
        ];
    }
}
