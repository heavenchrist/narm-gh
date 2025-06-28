<?php

namespace App\Filament\Portal\Pages;

use Exception;
use Throwable;
use App\Enums\Gender;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Js;
use App\Enums\MaritalStatus;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Auth\Authenticatable;
use Filament\Forms\Concerns\InteractsWithForms;

class MyProfile extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.portal.pages.my-profile';

    use InteractsWithForms;
    public ?array $data = [];

public function mount(): void
{
    $this->form->fill(
        auth()->user()->attributesToArray()
    );
}

public function form(Form $form): Form
{
    return $form
        ->schema([
            Group::make()->schema([
                Section::make('BIO DATA')->schema([

                TextInput::make('name')
                                ->required()
                                ->maxLength(255)->columnSpanFull()
                                ->prefixIcon('heroicon-s-pencil-square'),

            DatePicker::make('date_of_birth')
                    ->native(false)
                        ->maxDate(function(){
                            return now()->subYears(18);
                        })
                    ->date()
                    ->required()
                    ->weekStartsOnMonday()
                    ->prefixIcon('heroicon-s-calendar'),

            Select::make('gender')
                            ->label('Gender')
                        ->prefixIcon('heroicon-s-users')
                        ->enum(Gender::class)
                        ->required()
                        ->options(Gender::class)->searchable(),

            TextInput::make('place_of_birth')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-s-home'),

            Select::make('marital_status')->label('Marital Status')
                        ->enum(MaritalStatus::class)
                        ->options(MaritalStatus::class)->searchable()
                        ->required()
                        ->prefixIcon('heroicon-s-user-group'),

            ])->columnSpan(2)->columns(2),

            Section::make()->schema([

                FileUpload::make('image_url')
                        ->label('Photo')
                        ->avatar(),

            ])->icon('heroicon-o-camera')->columnSpan(1)
        ])->columns(3)->columnSpanFull(),

    Group::make()->schema([

        Section::make('Conatct Info')->schema([

            TextInput::make('email')
                ->email()
                ->required()
                ->prefixIcon('heroicon-s-at-symbol')
                ->maxLength(255),

            TextInput::make('telephone')
                ->regex('/(0)[0-9]{9}/')
                ->maxLength(15)
                ->required()
                ->unique(ignoreRecord:true)
                ->prefixIcon('heroicon-s-phone-arrow-down-left'),

            TextInput::make('residential_address')
                 ->columnSpanFull()
                 ->maxLength(255)
                 ->required()
                 ->prefixIcon('heroicon-s-home-modern'),

            TextInput::make('next_of_kin')
                    ->columnSpanFull()
                     ->maxLength(255)
                     ->prefixIcon('heroicon-s-user-plus'),

            TextInput::make('next_of_kin_contact')
                    ->regex('/(0)[0-9]{9}/')
                    ->maxLength(15)
                    ->requiredWith('next_of_kin')
                    ->tel()
                    ->unique(ignoreRecord:true)
                     ->prefixIcon('heroicon-s-phone-arrow-down-left'),

        ])->columnSpan(1)->columns(1),

        Section::make('Professional Info')->schema([

            TextInput::make('staff_id')
                ->disabled()
                ->unique(ignoreRecord:true)->readonly()->dehydrated(false)
                ->prefixIcon('heroicon-s-credit-card'),

            TextInput::make('pin_number')
                ->maxLength(255)
                ->required()
                ->regex('/^[0-9]{2}[a-zA-Z]{2}[\d]{6,}+$/')
                ->maxLength(15)
                ->unique(ignoreRecord:true)
                ->prefixIcon('heroicon-s-ticket'),

            TextInput::make('registration_number')
                ->maxLength(255)
                ->required()
                ->regex('/^[a-zA-Z]{2,}[\d]{1,}+$/')
                ->prefixIcon('heroicon-s-pencil'),

            Select::make('rank_id')->label('Rank')->required()
                ->relationship('rank','name',modifyQueryUsing:function(Builder $query){
                    $query->where('status',true);
                })
                ->preload()->searchable()->columnSpanFull()
                ->required()
                ->prefixIcon('heroicon-s-academic-cap'),

            TextInput::make('place_of_work')
                ->maxLength(255)->columnSpanFull()
                ->required()
                ->prefixIcon('heroicon-s-building-office-2'),

            Select::make('region_id')->label('Region')
                ->required()
                ->relationship('region','name',modifyQueryUsing:function(Builder $query){
                    $query->where('status',true);
                })->disabled()
                ->preload()
                ->searchable()
                ->columnSpanFull()
                ->prefixIcon('heroicon-s-globe-asia-australia'),

            TextInput::make('district')
                ->maxLength(255)
                ->columnSpanFull()
                ->required()
                ->prefixIcon('heroicon-s-map-pin'),

        ])->columnSpan(1)->columns(2),

    ])->columnSpanFull()->columns(2),
        ])
        ->statePath('data')
        ->model(auth()->user());
}

protected function getFormActions(): array
{
    return [
        Action::make('Update')
            ->color('primary')
            ->submit('Update'),
    ];
}



public function update(): void
{
    auth()->user()->update(
        $this->form->getState()
    );

    Notification::make()
        ->title('Profile update')
        ->body('Your Profile update was successful')
        ->success()
        ->send();
}

}