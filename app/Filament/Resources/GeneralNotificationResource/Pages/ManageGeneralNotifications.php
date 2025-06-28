<?php

namespace App\Filament\Resources\GeneralNotificationResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\GeneralNotificationResource;

class ManageGeneralNotifications extends ManageRecords
{
    protected static string $resource = GeneralNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create')
            ->icon('heroicon-s-plus-circle')
            ->modalWidth(MaxWidth::Large)
            ->modalSubmitActionLabel('Save')
            ->modalIcon('heroicon-o-pencil-square')
            ->closeModalByClickingAway(false)
            ->slideOver(),
        ];
    }
}
