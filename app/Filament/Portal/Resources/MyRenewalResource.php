<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\MyRenewalResource\Pages;
use App\Filament\Portal\Resources\MyRenewalResource\RelationManagers;
use App\Models\MyRenewal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MyRenewalResource extends Resource
{
    protected static ?string $model = MyRenewal::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('expiry_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('staff_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pin_ain')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('renewal_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMyRenewals::route('/'),
        ];
    }
}