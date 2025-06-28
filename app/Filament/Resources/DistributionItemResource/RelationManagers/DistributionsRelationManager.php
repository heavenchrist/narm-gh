<?php

namespace App\Filament\Resources\DistributionItemResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DistributionsRelationManager extends RelationManager
{
    protected static string $relationship = 'distributions';
     protected static bool $isLazy = false;
public function isReadOnly(): bool
{
    return false;
}

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('description')->required()->unique(ignoreRecord:true),
                Select::make('office_id')->relationship('office','name')
                        ->searchable()->preload(),
                Forms\Components\TextInput::make('quantity')->minValue(1)
                    ->required()->numeric(),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
           // ->recordTitle('Item Distributions')
            ->heading('Item Distributions Lists')
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('office.name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\IconColumn::make('is_received')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Assigned Date'),
                Tables\Columns\TextColumn::make('receivedBy.name')->label('Received By'),
                Tables\Columns\TextColumn::make('received_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Add')
                ->modalHeading("Add Item Distribution")
                ->icon('heroicon-s-plus-circle')
                ->modalWidth(MaxWidth::Medium)
                ->modalSubmitActionLabel('Save')
                ->modalIcon('heroicon-o-pencil-square')
                ->closeModalByClickingAway(false)
                ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->modalWidth(MaxWidth::Medium)
                ->modalHeading("Edit Item Distribution")
                ->modalIcon('heroicon-o-pencil-square')
                ->closeModalByClickingAway(false)
                ->slideOver()
                ->hidden(function($record){
                    return $record->received_by || $record->distributionLists()->exists();
                }),
                Tables\Actions\DeleteAction::make()
                ->hidden(function($record){
                    return $record->received_by || $record->distributionLists()->exists();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
