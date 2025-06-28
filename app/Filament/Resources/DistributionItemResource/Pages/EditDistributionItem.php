<?php

namespace App\Filament\Resources\DistributionItemResource\Pages;

use App\Filament\Resources\DistributionItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistributionItem extends EditRecord
{
    protected static string $resource = DistributionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
