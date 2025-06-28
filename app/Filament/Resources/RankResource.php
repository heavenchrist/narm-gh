<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Rank;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RankResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RankResource\RelationManagers;

class RankResource extends Resource
{
    protected static ?string $model = Rank::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->required()->unique(ignoreRecord:true)
                ->columnSpanFull()
                //->live(debounce:500)
                ->live(onBlur:true)
                ->maxLength(255)
                ->afterStateUpdated(function( Set $set, ?string $state,$operation){
                        if($operation==='create'){
                             $str = implode('', array_map(function($v){
                                if($v){
                                    return $v[0];
                                }
                              },
                                  explode(' ',trim($state))
                             ));
                    $set('shortname',Str::upper($str));
                   }
                }),
            Forms\Components\TextInput::make('shortname')->columnSpanFull()
                ->required()->unique(ignoreRecord:true)
                ->maxLength(255),
                Forms\Components\Toggle::make('status')->inline(false)->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction(null)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shortname')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
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
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::Medium)
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
            'index' => Pages\ManageRanks::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}
