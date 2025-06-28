<?php

namespace App\Filament\Region\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\Gender;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\MaritalStatus;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Region\Resources\UserResource\Pages;
use App\Filament\Region\Resources\UserResource\RelationManagers;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use App\Filament\Resources\UserResource\Widgets\MembershipStatsOverview;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use App\Filament\Region\Resources\UserResource\Widgets\RegionalMembershipOverview;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Regional Membership';

    protected static ?string $slug = 'region-membership';
 public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                            Forms\Components\Section::make('BIO DATA')->schema([

                            Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->disabled()
                                            ->maxLength(255)->columnSpanFull()
                                            ->prefixIcon('heroicon-s-pencil-square'),

                        Forms\Components\DatePicker::make('date_of_birth')
                                    ->native(false)
                                    ->maxDate(function(){
                                        return now()->subYears(18);
                                    })
                                    ->disabled()
                                    ->date()
                                    ->required()
                                    ->weekStartsOnMonday()
                                    ->prefixIcon('heroicon-s-calendar'),

                        Forms\Components\Select::make('gender')
                                        ->label('Gender')
                                    ->prefixIcon('heroicon-s-users')
                                    ->enum(Gender::class)
                                    ->disabled()
                                    ->options(Gender::class)->searchable(),

                        Forms\Components\TextInput::make('place_of_birth')
                                    ->columnSpanFull()
                                    ->maxLength(255)
                                    ->disabled()
                                    ->prefixIcon('heroicon-s-home'),

                        Forms\Components\Select::make('marital_status')->label('Marital Status')
                                    ->enum(MaritalStatus::class)
                                    ->options(MaritalStatus::class)->searchable()
                                    ->disabled()
                                    ->prefixIcon('heroicon-s-user-group'),

                        ])->columnSpan(2)->columns(2),

                        Forms\Components\Section::make()->schema([

                            Forms\Components\FileUpload::make('image_url')
                                    ->label('Photo')
                                    ->disabled()
                                    ->directory('photos')
                                    ->avatar(),

                        ])->icon('heroicon-o-camera')->columnSpan(1)
                    ])->columns(3)->columnSpanFull(),

                Forms\Components\Group::make()->schema([

                    Forms\Components\Section::make('Conatct Info')->schema([

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->disabled()
                            ->prefixIcon('heroicon-s-at-symbol')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telephone')
                            ->regex('/(0)[0-9]{9}/')
                            ->maxLength(15)
                            ->disabled()
                            ->unique(ignoreRecord:true)
                            ->prefixIcon('heroicon-s-phone-arrow-down-left'),

                        Forms\Components\TextInput::make('residential_address')
                             ->columnSpanFull()
                             ->maxLength(255)
                             ->disabled()
                             ->prefixIcon('heroicon-s-home-modern'),

                        Forms\Components\TextInput::make('next_of_kin')
                                ->disabled()
                                 ->maxLength(255)
                                 ->prefixIcon('heroicon-s-user-plus'),

                        Forms\Components\TextInput::make('next_of_kin_contact')
                                ->regex('/(0)[0-9]{9}/')
                                ->maxLength(15)
                                ->disabled()
                                ->tel()
                                ->unique(ignoreRecord:true)
                                 ->prefixIcon('heroicon-s-phone-arrow-down-left'),

                    ])->columnSpan(1)->columns(1),

                    Forms\Components\Section::make('Professional Info')->schema([

                        Forms\Components\TextInput::make('staff_id')
                            ->unique(ignoreRecord:true)
                            ->maxLength(15)
                            ->required()
                            ->prefixIcon('heroicon-s-credit-card'),

                        Forms\Components\TextInput::make('pin_number')
                            ->maxLength(255)
                            ->disabled()
                            ->regex('/^[0-9]{2}[a-zA-Z]{2}[\d]{6,}+$/')
                            ->maxLength(15)
                            ->unique(ignoreRecord:true)
                            ->prefixIcon('heroicon-s-ticket'),

                        Forms\Components\TextInput::make('registration_number')
                            ->maxLength(255)
                            ->disabled()
                            ->regex('/^[a-zA-Z]{2,}[\d]{1,}+$/')
                            ->prefixIcon('heroicon-s-pencil'),

                        Forms\Components\Select::make('rank_id')->label('Rank')->required()
                            ->relationship('rank','name',modifyQueryUsing:function(Builder $query){
                                $query->where('status',true);
                            })
                            ->preload()->searchable()->columnSpanFull()
                            ->disabled()
                            ->prefixIcon('heroicon-s-academic-cap'),

                        Forms\Components\TextInput::make('place_of_work')
                            ->maxLength(255)->columnSpanFull()
                            ->disabled()
                            ->prefixIcon('heroicon-s-building-office-2'),

                        Forms\Components\Select::make('region_id')->label('Region')
                            ->required()
                            ->relationship('region','name',modifyQueryUsing:function(Builder $query){
                                $query->where('status',true);
                            })
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->prefixIcon('heroicon-s-globe-asia-australia'),

                        Forms\Components\TextInput::make('district')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->disabled()
                            ->prefixIcon('heroicon-s-map-pin'),

                    ])->columnSpan(1)->columns(2),

                ])->columnSpanFull()->columns(2),






            ]);
    }
  /*   public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
            ->schema([
              ImageEntry::make('image_url')->label('Photo')
              ->getStateUsing(function($record){
                return $record->image_url ? Storage::url($record->image_url) : null;
              })
                ->size(40)
                 ->circular()->defaultImageUrl(url('/images/placeholder.png')),
                TextEntry::make('name'),
                TextEntry::make('staff_id'),
                TextEntry::make('email'),
                TextEntry::make('date_of_birth'),
                TextEntry::make('place_of_birth'),
                TextEntry::make('residential_address'),
                TextEntry::make('telephone'),
                TextEntry::make('pin_number'),
                TextEntry::make('registration_number'),
                TextEntry::make('place_of_work'),
               TextEntry::make('rank.name'),
               TextEntry::make('region.name'),
                TextEntry::make('district'),
                TextEntry::make('gender'),
                TextEntry::make('marital_status'),
                TextEntry::make('next_of_kin'),
                TextEntry::make('next_of_kin_contact'),

            ]);
    } */

   public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(' ')
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Photo')
                        ->circular()
                    ->defaultImageUrl(function($record){
                        if($record->gender?->name == 'male'){
                        return  url('/placeholders/male.jpg');
                        }
                        return  url('/placeholders/female.jpg');
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('staff_id')
                    ->searchable()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('telephone')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pin_number')
                    ->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('registration_number')
                    ->searchable()->toggleable(),

                Tables\Columns\TextColumn::make('region.name')
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('gender')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('marital_status')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                                    ->label('Gender')
                                    ->options(Gender::class),
                SelectFilter::make('marital_status')
                                    ->label('Marital Status')
                                    ->options(MaritalStatus::class),
                QueryBuilder::make()
                    ->constraints([
                        DateConstraint::make('created_at')->label('Account Created Date'),
                        DateConstraint::make('date_of_birth')->label('Date of Birth'),
                        RelationshipConstraint::make('region')->label('Regions')
                                    ->multiple()
                                    ->selectable(
                                        IsRelatedToOperator::make()
                                            ->titleAttribute('name')
                                            ->searchable()
                                            ->multiple(),
                                    ),

                    ]),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->deferFilters()
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\ViewAction::make()
                ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                   // Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('is_admin', false)->where('region_id', auth()->user()?->region_id);
    }

    public static function getWidgets(): array
    {
        return [
            RegionalMembershipOverview::class,
        ];

    }
}
