<?php

namespace App\Filament\Portal\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Training;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Portal\Resources\TrainingResource\Pages;
use App\Filament\Portal\Resources\TrainingResource\RelationManagers;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('content')->markdown()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->modifyQueryUsing(function($query){
                return $query->with('trainingRegistration','trainingRegistrations');
            })
            ->defaultSort('created_at','desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label("Topic")
                ->description(function($record){
                    return new HtmlString("<b>Mode: </b>".$record->training_mode->value);
                })
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_end_date')
                    ->dateTime()
                    ->description(function($record){
                        return $record->registration_end_date->diffForHumans();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('start')->label('Training Starts')
                    ->dateTime()
                     ->description(function($record){
                        return $record->registration_end_date->diffForHumans();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')->label('Training Ends')
                    ->dateTime()
                     ->description(function($record){
                        return $record->registration_end_date->diffForHumans();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('trainingStatus')
                    ->getStateUsing(function($record){
                        return $record->hasRegistered() ? 'Registered' : 'Not Registered';
                    })
                    ->badge()
                    ->color(function($state){
                        return $state == 'Not Registered' ? Color::Gray : Color::Emerald;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth(MaxWidth::Large)
                        ->closeModalByClickingAway(false),
                Tables\Actions\Action::make('register')
                   ->hidden(fn ($record) =>  $record->hasRegistered() || $record->registration_end_date->isPast() )
                    ->requiresConfirmation()
                     ->modalHeading('System Warning')
                    ->modalDescription('Do you want register for this training?')
                    ->modalSubmitActionLabel('Yes')
                    ->modalCancelActionLabel('Close')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->action(function($record){
                        $record->trainingRegistration()->create([
                            'member_id'=>auth()->user()->id,
                            //'training_id'=> $record->id,
                        ]);
                        return Notification::make()
                                    ->title('System Response')
                                    ->body('Resgistration was successful')
                                    ->info()
                                    ->send();
                    }),
                Tables\Actions\Action::make('unregister')
                    ->color('danger')
                     ->visible(fn ($record) => $record->registration_end_date->isFuture() && $record->hasRegistered()  )
                    ->requiresConfirmation()
                    ->modalHeading('System Warning')
                    ->modalDescription('Do you want cancel your registration for this training?')
                    ->modalSubmitActionLabel('Yes')
                    ->modalCancelActionLabel('Close')
                    ->icon('heroicon-o-arrow-left-start-on-rectangle')
                    ->action(function($record){
                        $record->trainingRegistration()->where('member_id',auth()->user()->id)->delete();
                        return Notification::make()
                                    ->title('System Response')
                                    ->body('Resgistration was successfully cancelled')
                                    ->info()
                                    ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                  //  Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTrainings::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['trainingRegistrations' => function ($query) {
                         $query->where('member_id', auth()->id());
                }])->where('status',true)
                ->where('region_id',auth()->user()->region_id)
                ->orWhereNull('region_id');
    }
}
