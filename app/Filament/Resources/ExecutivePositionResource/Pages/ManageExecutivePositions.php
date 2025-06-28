<?php

namespace App\Filament\Resources\ExecutivePositionResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\ExecutivePositionResource;

class ManageExecutivePositions extends ManageRecords
{
    protected static string $resource = ExecutivePositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create')
            ->icon('heroicon-s-plus-circle')
            ->modalWidth(MaxWidth::Medium)
            ->modalSubmitActionLabel('Save')
            ->modalIcon('heroicon-o-pencil-square')
            ->closeModalByClickingAway(false)
            ->slideOver(),
        ];
    }
}
