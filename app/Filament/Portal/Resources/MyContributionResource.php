<?php

namespace App\Filament\Portal\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MyContribution;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Portal\Resources\MyContributionResource\Pages;
use App\Filament\Portal\Resources\MyContributionResource\RelationManagers;

class MyContributionResource extends Resource
{
    protected static ?string $model = MyContribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';



    public static function table(Table $table): Table
    {

        return $table
            ->defaultSort('period','desc')
            ->columns([
                Tables\Columns\TextColumn::make('staff_id')
                ->label('Staff ID'),
                //->searchable(),
                /* Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('place_of_work')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable(), */
                Tables\Columns\TextColumn::make('period')
                   // ->searchable()
                    ->sortable()->formatStateUsing(function($record){
                        // convert to carbon date format
                        $date = Carbon::parse($record->period);
                        return $date->format('F, Y');
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->money(currency: 'GHS')
                    ->summarize(summarizers: Sum::make()->label('Grand Total')->money(currency: 'GHS',divideBy: 100))
                    ->sortable(),
                    //->summarize(Sum::make()->label('Total Contributions')),
               /*  Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_updated')
                    ->boolean(), */
            ])->defaultSort('period','desc')
            ->filters([
                SelectFilter::make('period')->options(function(){
                    return MyContribution::distinct()->pluck('period')->mapWithKeys(function($period){
                        return [$period => Carbon::parse($period)->format('F, Y')];
                    });

                })->searchable()
            ])->deferFilters()
            ->filtersApplyAction(
            fn (Action $action) => $action
                ->button()
                ->label('Search Now'),
            )
            ->filtersTriggerAction(
            fn (Action $action) => $action
                ->button()
                ->label('Search'),
              )
            ->actions([
                /* Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), */
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMyContributions::route('/'),
        ];
    }
}
