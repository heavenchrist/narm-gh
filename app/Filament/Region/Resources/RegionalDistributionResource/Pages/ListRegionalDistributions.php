<?php

namespace App\Filament\Region\Resources\RegionalDistributionResource\Pages;

use App\Filament\Region\Resources\RegionalDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegionalDistributions extends ListRecords
{
    protected static string $resource = RegionalDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}