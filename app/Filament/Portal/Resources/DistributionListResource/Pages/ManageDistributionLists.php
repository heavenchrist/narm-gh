<?php

namespace App\Filament\Portal\Resources\DistributionListResource\Pages;

use App\Filament\Portal\Resources\DistributionListResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDistributionLists extends ManageRecords
{
    protected static string $resource = DistributionListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}