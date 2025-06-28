<?php

namespace App\Filament\Region\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DistributionList;
use Filament\Resources\Resource;
use App\Traits\ChartAnimationTrait;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Region\Resources\DistributionListResource\Pages;
use App\Filament\Region\Resources\DistributionListResource\RelationManagers;

class DistributionListResource extends Resource
{
    protected static ?string $model = DistributionList::class;



    protected static ?string $navigationIcon = 'heroicon-o-share';

    //protected static ?string $navigationParentItem = 'Regional Distribution Items';

    public static function table(Table $table): Table
    {
        return $table
        //->defaultGroup('distributionItem.name')
            ->columns([
                Tables\Columns\TextColumn::make('member.name')
                    ->numeric()
                    ->sortable(),

                    Tables\Columns\TextColumn::make('distributionItem.name')
                    ->numeric()
                    ->description(function($record){
                        return $record?->distribution?->description;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Assigned By')
                    ->numeric()->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->badge()
                    ->numeric()
                    ->alignCenter()
                    ->color(ChartAnimationTrait::color())
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_received')
                    ->boolean()->alignCenter(),
                Tables\Columns\TextColumn::make('received_date')
                    ->dateTime()
                    ->description(function($record){
                        return $record?->received_date?->diffForHumans();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Assigned Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('quantity')
                    ->alignCenter()
                    ->badge()
                    ->color(ChartAnimationTrait::color())
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')->numeric()),

            ])
            ->filters([
                SelectFilter::make('distributionItem')
                        ->relationship('distributionItem','name')
                        ->searchable()
                        ->preload(),
                SelectFilter::make('distribution')
                        ->relationship('distribution','description')
                        ->searchable()
                        ->preload()
            ])->deferFilters()
            ->filtersApplyAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Search'),
            )->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\DeleteAction::make()->databaseTransaction()->label('Reverse')
                    ->hidden(function($record){
                        return $record->is_received;
                    })->before(function($record){
                        //put this in try catch
                        try {
                            $record->distribution->increment('quantity',$record->quantity);
                            $record->distribution->decrement('distribution_count',$record->quantity);
                        } catch (\Throwable $th) {
                            //throw $th;
                        }

                        //"quantity" => 1
                    }),
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
            'index' => Pages\ManageDistributionLists::route('/'),
        ];
    }
}
