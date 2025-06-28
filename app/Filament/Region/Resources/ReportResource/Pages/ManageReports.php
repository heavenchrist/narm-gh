<?php

namespace App\Filament\Region\Resources\ReportResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Region\Resources\ReportResource;

class ManageReports extends ManageRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()  ->label('Add')
            ->modalHeading("Add Report")
            ->icon('heroicon-s-plus-circle')
            ->modalWidth(MaxWidth::Medium)
            ->modalSubmitActionLabel('Save')
            ->createAnother(false)
            ->modalIcon('heroicon-o-pencil-square')
            ->slideOver(),
        ];
    }
}