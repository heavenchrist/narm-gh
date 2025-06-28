<?php

namespace App\Filament\Resources\OfficeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Region;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OfficeExecutiveRelationManager extends RelationManager
{
    protected static string $relationship = 'officeExecutive';
     protected static bool $isLazy = false;
     public function isReadOnly(): bool
        {
            return false;
        }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('region_id')->label('Region')
                    ->options(Region::orderBy('name','asc')->pluck('name', 'id')->toArray())
                    ->preload()->searchable()
                    ->required(),
                Forms\Components\Select::make('executive_position_id')
                    ->relationship('executivePosition', 'name')
                    ->preload()->searchable()
                    ->required(),
                Forms\Components\TextInput::make('telephone')
                    ->required()
                    ->maxLength(255),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
        ->heading('Executive Positions')
            ->recordTitle('Executive Position')
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('executivePosition.name'),
                Tables\Columns\TextColumn::make('telephone'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Add')
                ->modalHeading("Add an Executive")
                ->icon('heroicon-s-plus-circle')
                ->modalWidth(MaxWidth::Medium)
                ->modalSubmitActionLabel('Save')
                ->modalIcon('heroicon-o-pencil-square')
                ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->icon('heroicon-s-plus-circle')
                ->modalWidth(MaxWidth::Medium)
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
}
