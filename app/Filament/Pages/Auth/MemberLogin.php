<?php

namespace App\Filament\Pages\Auth;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class MemberLogin extends BaseAuth
{
  public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
    public function getTitle(): string | Htmlable
    {
        return __('filament-panels::pages/auth/login.title');
    }

     protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('Login'))
            ->icon('heroicon-m-arrow-right')
            ->submit('authenticate');
    }

    public function getHeading(): string | Htmlable
    {
        return __(key: 'Member Login');
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Employee ID')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->placeholder('Enter your Employee ID')
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'staff_id';

        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
