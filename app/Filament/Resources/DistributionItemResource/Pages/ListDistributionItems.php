<?php

namespace App\Filament\Resources\DistributionItemResource\Pages;

use App\Filament\Resources\DistributionItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistributionItems extends ListRecords
{
    protected static string $resource = DistributionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create')
            ->icon('heroicon-s-plus-circle'),
        ];
    }
}