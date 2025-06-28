<?php

namespace App\Filament\Region\Resources\TrainingResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Region\Resources\TrainingResource;

class CreateTraining extends CreateRecord
{
    protected static string $resource = TrainingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['region_id'] = auth()->user()->region_id;

        return $data;
    }

    /* protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->requiresConfirmation()
            ->action(fn () => $this->create())
            ->keyBindings(['mod+s']);
    } */

protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

protected function getCreatedNotification(): ?Notification
{
    return Notification::make()
        ->success()
        ->title('Record registered')
        ->body('The record has been created successfully.');
}
}
