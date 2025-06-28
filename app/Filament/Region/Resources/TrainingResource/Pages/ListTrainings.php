<?php

namespace App\Filament\Region\Resources\TrainingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Region\Resources\TrainingResource;
use App\Filament\Region\Resources\TrainingResource\RelationManagers\TrainingRegistrationsRelationManager;

class ListTrainings extends ListRecords
{
    protected static string $resource = TrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

     public static function getRelations(): array
    {
        return [
            TrainingRegistrationsRelationManager::class,
        ];
    }
}