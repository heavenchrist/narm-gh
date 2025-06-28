<?php

namespace App\Filament\Resources\RenewalResource\Pages;

use Filament\Actions;
use Filament\Actions\ImportAction;
use App\Filament\Imports\RenewalImporter;
use App\Filament\Resources\RenewalResource;
use App\Filament\Resources\RenewalResource\Widgets\RenewalChart;
use Filament\Resources\Pages\ManageRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\RenewalResource\Widgets\RenewalStatsOverview;

class ManageRenewals extends ManageRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = RenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make('importRenewals')
            ->icon('heroicon-o-arrow-up-on-square-stack')
                ->importer(RenewalImporter::class)
                ->chunkSize(100),
        ];
    }

    //add widget
    protected function getHeaderWidgets(): array
    {
        return [
            // Add your widgets here
RenewalStatsOverview::class,
            RenewalChart::class,

        ];
    }

}
