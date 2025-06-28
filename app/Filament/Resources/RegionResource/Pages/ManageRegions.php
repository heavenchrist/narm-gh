<?php

namespace App\Filament\Resources\RegionResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\RegionResource;
use Filament\Resources\Pages\ManageRecords;

class ManageRegions extends ManageRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create')
            ->icon('heroicon-s-plus-circle')
            ->modalWidth(MaxWidth::Medium)
            ->modalSubmitActionLabel('Save')
            ->modalIcon('heroicon-o-pencil-square')
            ->slideOver(),
        ];
    }
}