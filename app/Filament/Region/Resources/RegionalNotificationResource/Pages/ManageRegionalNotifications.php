<?php

namespace App\Filament\Region\Resources\RegionalNotificationResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Region\Resources\RegionalNotificationResource;

class ManageRegionalNotifications extends ManageRecords
{
    protected static string $resource = RegionalNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create')
            ->icon('heroicon-s-plus-circle')
            ->modalWidth(MaxWidth::Large)
            ->modalSubmitActionLabel('Save')
            ->modalIcon('heroicon-o-pencil-square')
            ->slideOver(),
        ];
    }
}