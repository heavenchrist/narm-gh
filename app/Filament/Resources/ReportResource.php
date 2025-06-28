<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Indicator;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ReportResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReportResource\RelationManagers;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('report_url')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('office_id')
                    ->relationship('office', 'name')
                    ->required(),
                Forms\Components\Select::make('region_id')
                    ->relationship('region', 'name')
                    ->required(),
                Forms\Components\TextInput::make('received_by')
                    ->numeric(),
                Forms\Components\Toggle::make('is_submitted')
                    ->required(),
                Forms\Components\Toggle::make('is_received')
                    ->required(),
                Forms\Components\DateTimePicker::make('received_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')->label('Submitted By')
                    ->description(function($record){
                        return $record->region->name;
                    })->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('receivedBy.name')
                    ->numeric()->searchable()
                    ->description(function($record){
                        return $record->received_date?->diffForHumans();
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('received_date')->badge()
                    ->dateTime()->toggleable(isToggledHiddenByDefault: true)
                    /* ->description(function($record){
                        return $record->received_date?->diffForHumans();
                    }) */
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Select::make('region')->relationship('region','name')->preload()->searchable(),
                        DatePicker::make('received_from')->native(false)->maxDate(now()),
                        DatePicker::make('received_to')->native(false)->afterOrEqual('received_from')->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                        ->when(
                            $data['region'],
                            fn (Builder $query, $regionId): Builder => $query->where('region_id', '=', $regionId),
                        )
                            ->when(
                                $data['received_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('received_date', '>=', $date),
                            )
                            ->when(
                                $data['received_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('received_date', '<=', $date),
                            );
                    })->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['received_from'] ?? null) {
                            $indicators[] = Indicator::make('Received from ' . Carbon::parse($data['received_from'])->toFormattedDateString())
                                ->removeField('received_from');
                        }

                        if ($data['received_to'] ?? null) {
                            $indicators[] = Indicator::make('Received to ' . Carbon::parse($data['received_to'])->toFormattedDateString())
                                ->removeField('received_to');
                        }

                        return $indicators;
                    })
            ])->deferFilters()
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                MediaAction::make('viewReport')
                ->color(Color::Red)
                    ->icon('heroicon-o-document-text')
                    ->closeModalByClickingAway(false)
                    ->media(fn($record)=>Storage::url($record?->report_url))
                    ->visible(function($record){
                        return $record?->report_url &&  $record->is_received  ? true:false;
                    }) ,
                Tables\Actions\Action::make('receive')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color(Color::Purple)
                            ->requiresConfirmation()
                            ->modalSubmitActionLabel('Yes')
                            ->modalCancelActionLabel('No')
                    ->action(function($record){
                        //$record->where('is_received',false)->update(['is_submitted',false]);
                        //dd($record->where('is_received',false)->update(['is_submitted'=>false]));
                       //if(Report::where('is_received',false)->where('id',$record->id)->update(['is_submitted'=>false])){
                         if($record->update(['is_received'=>true,'received_by'=>auth()->user()->id,'received_date'=>now()])){
                            return Notification::make()->info()
                                ->title('System Notification')
                                ->body('Report received successfully')->send();
                        }else{
                            return Notification::make()->info()
                                ->title('System Notification')
                                ->body('Report received unsuccessfully')->send();
                        }
                })->visible(function($record){
                    return $record?->report_url ? true:false;
                })->hidden(function($record){
                    return $record->is_received  ? true:false;
                })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->withoutGlobalScopes()->where('is_submitted',true);
}
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
        ];
    }
}
