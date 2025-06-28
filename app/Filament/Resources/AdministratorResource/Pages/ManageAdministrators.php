<?php

namespace App\Filament\Resources\AdministratorResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\AdministratorResource;

class ManageAdministrators extends ManageRecords
{
    protected static string $resource = AdministratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make() ->label('Create')
            ->icon('heroicon-s-plus-circle')
            ->modalWidth(MaxWidth::Medium)
            ->modalSubmitActionLabel('Save')
            ->modalIcon('heroicon-o-pencil-square')
            ->slideOver(),
        ];
    }
}