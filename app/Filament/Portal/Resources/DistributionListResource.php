<?php

namespace App\Filament\Portal\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DistributionList;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Portal\Resources\DistributionListResource\Pages;
use App\Filament\Portal\Resources\DistributionListResource\RelationManagers;

class DistributionListResource extends Resource
{
    protected static ?string $model = DistributionList::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $modelLabel = 'My Items';

    protected static ?string $slug = 'my-regional-items';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('distribution.description')->label('Item Description')
                    /* ->getStateUsing(function($record){
                        dd($record->distribution);
                    }) */
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region.name')
                    ->description(function($record){
                        return $record?->office?->name;
                    })
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()->label('Assigned By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Assigned Date')
                    ->dateTime()
                    ->description(function($record){
                        return $record?->created_at?->diffForHumans();
                    })
                    ->sortable(),
                /* Tables\Columns\IconColumn::make('is_received')
                        ->hidden(function($record){
                            return $record?->is_received ? true : false;
                        })
                    ->boolean(), */
                Tables\Columns\TextColumn::make('received_date')
                    ->dateTime()
                    //->descriptionIcon('heroicon-o-calendar')
                     ->description(function($record){

                        return $record?->received_date?->diffForHumans();

                    })
                   /*  ->hidden(function($record){
                        return $record?->received_date ? true : false;
                    }) */
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('receive_item')->action(function($record){
                    $record->update([
                        'is_received' => true,
                        'received_date' => now(),
                    ]);
                })->hidden(function($record){
                    return $record->is_received;
                })->requiresConfirmation()->modalSubmitActionLabel('Yes')
                ->icon('heroicon-o-shopping-cart')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDistributionLists::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('member_id', auth()->user()->id);
    }
}