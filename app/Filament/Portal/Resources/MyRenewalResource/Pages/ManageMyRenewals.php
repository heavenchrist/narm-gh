<?php

namespace App\Filament\Portal\Resources\MyRenewalResource\Pages;

use App\Filament\Portal\Resources\MyRenewalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMyRenewals extends ManageRecords
{
    protected static string $resource = MyRenewalResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}
