<?php

namespace App\Filament\Portal\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use App\Models\GeneralNotification;
use Filament\Support\Enums\MaxWidth;
use App\Models\GeneralNotificationRead;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Portal\Resources\GeneralNotificationResource\Pages;
use App\Filament\Portal\Resources\GeneralNotificationResource\RelationManagers;

class GeneralNotificationResource extends Resource
{
    protected static ?string $model = GeneralNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';



    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name')->label('Title')
                    ->columnSpanFull(),
                TextEntry::make('content')->markdown()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction(null)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->words(15),
                Tables\Columns\TextColumn::make('read')
                ->label('Read')->getStateUsing(function($record){
                   // dd($record->generalNotificationRead);
                   if($record->generalNotificationRead->where('member_id',auth()->user()->id)->count() > 0) {
                     return 'Yes';
                     }else{
                     return 'No';
                     }

                })->badge()
                ->color(fn (string $state): string => match ($state) {
                    'No' => 'warning',
                    'Yes' => 'success',
                }),
                Tables\Columns\TextColumn::make('created_at')->label('Posted At')
                    ->dateTime()
                    ->description(function($record){
                        return $record->created_at?->diffForHumans();
                    })
                    ->sortable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View')
                ->modalWidth(MaxWidth::Large)
                ->modalCancelActionLabel('Ok')
                ->modalIcon('heroicon-o-eye')
                ->slideOver()
                ->mutateRecordDataUsing(function (array $data): array {
                    // update GeneralNotificationRead

                    GeneralNotificationRead::firstOrNew(
                            ['general_notification_id' => $data['id'],
                            'member_id'=>auth()->user()->id]
                            )->save();

                    return $data;
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGeneralNotifications::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes()->where('status', true)
            ->where('region_only',false)
            ->orWhere('region_id',auth()->user()->region_id);
    }
}