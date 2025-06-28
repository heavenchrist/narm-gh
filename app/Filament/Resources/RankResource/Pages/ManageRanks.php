<?php

namespace App\Filament\Resources\RankResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\RankResource;
use Filament\Resources\Pages\ManageRecords;

class ManageRanks extends ManageRecords
{
    protected static string $resource = RankResource::class;

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