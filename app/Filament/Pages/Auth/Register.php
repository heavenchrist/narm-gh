<?php

namespace App\Filament\Pages\Auth;

use App\Models\RegistrationNumber;
use App\Rules\PinRegNumValidation;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{


    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPinFormComponent(),
                        $this->getRegistrationNumberFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
    protected function getPinFormComponent(): Component
    {
        return TextInput::make('pin_ain')
              ->regex('/^[0-9]{2}[a-zA-Z]{2}[\d]{6,}+$/')
              ->unique($this->getUserModel())
               ->rules([new PinRegNumValidation()])
            ->label('PIN/AIN')
            ->validationMessages([
                'unique' => 'The PIN/AIN has already been registered.',
                'regex' => 'The PIN/AIN format is invalid.',
            ])
            ->required()
            ->minLength(10)
            ->maxLength(20);
    }
    protected function getRegistrationNumberFormComponent(): Component
    {
        return TextInput::make('registration_number')
            //->label('Hello')
            //->label('PIN/AIN')
            ->required()
            ->regex('/^[a-zA-Z]{2,}[\d]{1,}+$/')
            ->unique(RegistrationNumber::class)
            ->minLength(3)
            ->maxLength(10);
    }
    

    protected function handleRegistration(array $data): Model
    {
       
        return $this->getUserModel()::create($data);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['name'] =session($data['pin_ain']);
            unset($data['registration_number']);
            session()->forget($data['pin_ain']);
        return $data;
    }
}
