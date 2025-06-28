<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\GeneralNotification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GeneralNotificationResource\Pages;
use App\Filament\Resources\GeneralNotificationResource\RelationManagers;

class GeneralNotificationResource extends Resource
{
    protected static ?string $model = GeneralNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Title')
                    ->required()->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\MarkdownEditor::make('content')
                    ->required()->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Select::make('region_id')
                    ->relationship('region', 'name'),
                Forms\Components\DatePicker::make('expiry_date')->native(false)->minDate(now()),


                Forms\Components\Toggle::make('status')
                    ->required()->inline(false)->hiddenOn('create'),
            ])->columns(1);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')
                    ->extraAttributes(['style' => 'font-family: monospace;']),
                TextEntry::make('region')
                    ->extraAttributes(['style' => 'font-family: monospace;'])
                ->getStateUsing(function($record){
                        if(!$record->region_id){
                        return 'All Regions';
                        }
                        return $record->region->name;
                    }),
                TextEntry::make('content')->markdown(true)
                ->extraAttributes(['style' => 'font-family: monospace;'])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction(null)
        ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('read')
                    ->label('Read')->getStateUsing(function($record){
                       // dd($record->generalNotificationRead);
                       return $record->generalNotificationRead->count();

                    })->badge()->icon('heroicon-o-eye')
                    ->color('success'),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('region.name')
                    ->numeric()
                    ->getStateUsing(function($record){
                        if(!$record->region_id){
                        return 'All Regions';
                        }
                        return $record->region->name;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->closeModalByClickingAway(false)
                    ->modalWidth(MaxWidth::Large)
                    ->slideOver(),
                Tables\Actions\EditAction::make()
                    ->modalWidth(MaxWidth::Large)
                    ->modalSubmitActionLabel('Save')
                    ->modalIcon('heroicon-o-pencil-square')
                    ->closeModalByClickingAway(false)
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGeneralNotifications::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}