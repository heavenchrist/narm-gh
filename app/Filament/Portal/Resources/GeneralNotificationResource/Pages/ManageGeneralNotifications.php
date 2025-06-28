<?php

namespace App\Filament\Portal\Resources\GeneralNotificationResource\Pages;

use App\Filament\Portal\Resources\GeneralNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGeneralNotifications extends ManageRecords
{
    protected static string $resource = GeneralNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}