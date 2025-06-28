<?php

namespace App\Filament\Region\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\RegionalNotification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Region\Resources\RegionalNotificationResource\Pages;
use App\Filament\Region\Resources\RegionalNotificationResource\RelationManagers;

class RegionalNotificationResource extends Resource
{
    protected static ?string $model = RegionalNotification::class;

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
                Forms\Components\DatePicker::make('expiry_date')->native(false)->minDate(now()),


                Forms\Components\Toggle::make('status')
                    ->required()->inline(false)->hiddenOn('create'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction(null)
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
                Tables\Actions\EditAction::make()
                ->modalWidth(MaxWidth::Large)
                ->modalSubmitActionLabel('Save')
                ->modalIcon('heroicon-o-pencil-square')
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
            'index' => Pages\ManageRegionalNotifications::route('/'),
        ];
    }
}