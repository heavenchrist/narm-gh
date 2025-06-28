<?php

namespace App\Filament\Region\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Training;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\TrainingMode;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Region\Resources\TrainingResource\Pages;
use App\Filament\Region\Resources\TrainingResource\RelationManagers;
use App\Filament\Region\Resources\TrainingResource\RelationManagers\TrainingRegistrationsRelationManager;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Group::make()->schema([
                 Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')->label('Topic')
                        ->required()
                        ->placeholder('enter the topic for the training')
                        ->unique(ignoreRecord:true,modifyRuleUsing:function($rule){
                            return $rule->where('region_id',auth()->user()->region_id);
                        })
                        ->maxLength(255),
                    Forms\Components\Radio::make('training_mode')
                        ->required()
                        ->enum(TrainingMode::class)
                        ->options(TrainingMode::class)
                        ->inline()
                        ->inlineLabel(false),
                    Forms\Components\RichEditor::make('content')
                        ->required()
                        ->columnSpanFull(),/*
                    Forms\Components\Select::make('region_id')
                    ->relationship('region', 'name'), */
                 ])->columnSpan(2),
                Forms\Components\Section::make()->schema([
                    Forms\Components\DateTimePicker::make('registration_end_date')
                        ->native(false)
                        ->seconds(false)
                        ->required()->minDate(now()->format('Y-m-d'))
                        ->afterOrEqual('registration_end_date'),
                    Forms\Components\DateTimePicker::make('start')
                        ->native(false)
                        ->seconds(false)
                        ->required(),
                    Forms\Components\DateTimePicker::make('end')
                        ->native(false)
                        ->seconds(false)
                        ->required()
                        ->afterOrEqual('start'),
                    Forms\Components\Toggle::make('status')
                        ->inline(false)
                        ->required(),
                ])->columnSpan(1)->columns(1)

        ])->columns(3)->columnSpanFull()


    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Topic')
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
                        return $record->start->diffForHumans();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')->label('Training Ends')
                    ->dateTime()
                     ->description(function($record){
                        return $record->end->diffForHumans();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('training_mode')->badge()
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
               Tables\Columns\TextColumn::make('training_registrations_count') // uses the eager-loaded count
                    ->label('Registered')
                    ->badge()
                    ->action(fn ($record) => redirect(static::getUrl('index') . "/{$record->id}?activeRelationManager=0")),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TrainingRegistrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'view' => Pages\ViewTraining::route('/{record}'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
        ];
    }
    //scope modal
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
        ->withCount('trainingRegistrations')
        ->where('region_id',auth()->user()->region_id);

    }
}
