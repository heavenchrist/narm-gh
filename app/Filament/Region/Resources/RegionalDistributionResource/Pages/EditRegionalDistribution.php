<?php

namespace App\Filament\Region\Resources\RegionalDistributionResource\Pages;

use App\Filament\Region\Resources\RegionalDistributionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegionalDistribution extends EditRecord
{
    protected static string $resource = RegionalDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
