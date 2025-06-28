<?php

namespace App\Filament\Region\Resources\TrainingResource\Pages;

use App\Filament\Region\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTraining extends ViewRecord
{
    protected static string $resource = TrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    public function getContentTabIcon(): ?string
    {
        return 'heroicon-m-academic-cap';
    }

    public function getContentTabTitle(): ?string
    {
        return 'Training Name';
    }
}
