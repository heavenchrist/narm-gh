<?php

namespace App\Filament\Portal\Resources\TrainingResource\Pages;

use App\Filament\Portal\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTrainings extends ManageRecords
{
    protected static string $resource = TrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }
}
