<?php

namespace App\Filament\Region\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Report;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Region\Resources\ReportResource\Pages;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;
use App\Filament\Region\Resources\ReportResource\RelationManagers;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Title')
                    ->required()
                    ->maxLength(255)->columnSpanFull(),
                Forms\Components\FileUpload::make('report_url')->label('Report')
                    ->required()->directory('reports')
                    ->acceptedFileTypes(['application/pdf'])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
       // dd(auth()->user()->region->first()->office);
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()->label('Created by')->searchable()
                    ->sortable(),
                    Tables\Columns\ToggleColumn::make('is_submitted')
                    ->disabled(function($record){
                        return $record->is_submitted;
                    }),
                  Tables\Columns\IconColumn::make('is_received')
                    ->boolean(),
                    Tables\Columns\TextColumn::make('receivedBy.name')
                    ->numeric()->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('received_date')
                    ->dateTime()->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                MediaAction::make('viewReport')
                           ->color(Color::Red)
                           ->closeModalByClickingAway(false)
                               ->icon('heroicon-o-document-text')
                               ->media(fn($record)=>Storage::url($record?->report_url))->visible(function($record){
                                   return $record?->report_url ? true:false;
                               }),
                Tables\Actions\Action::make('revoke')
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->color(Color::Purple)
                            ->requiresConfirmation()
                            ->modalSubmitActionLabel('Yes')
                            ->modalCancelActionLabel('No')
                    ->action(function($record){
                        //$record->where('is_received',false)->update(['is_submitted',false]);
                        //dd($record->where('is_received',false)->update(['is_submitted'=>false]));
                       //if(Report::where('is_received',false)->where('id',$record->id)->update(['is_submitted'=>false])){
                         if($record->where('is_received',false)->update(['is_submitted'=>false])){
                            return Notification::make()->info()
                                ->title('System Notification')
                                ->body('Report revoked successfully')->send();
                        }else{
                            return Notification::make()->info()
                                ->title('System Notification')
                                ->body('Report revoked unsuccessfully')->send();
                        }
                })->visible(function($record){
                    return $record->is_submitted;
                }) ->hidden(function($record){
                    return $record->is_received;
                }),
                Tables\Actions\EditAction::make()
                ->modalHeading("Add Report")
                ->modalWidth(MaxWidth::Medium)
                ->modalSubmitActionLabel('Save')
                ->modalIcon('heroicon-o-pencil-square')
                ->slideOver()
                ->hidden(function($record){
                    return $record->is_submitted;
                }),
                Tables\Actions\DeleteAction::make()
                ->hidden(function($record){
                    return $record->is_submitted;
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes()->where('office_id',  auth()->user()?->office?->id);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
        ];
    }
}
