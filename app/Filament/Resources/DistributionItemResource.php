<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DistributionItem;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DistributionItemResource\Pages;
use App\Filament\Resources\DistributionItemResource\RelationManagers;
use App\Filament\Resources\DistributionItemResource\RelationManagers\DistributionsRelationManager;

class DistributionItemResource extends Resource
{
    protected static ?string $model = DistributionItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('name')->label('Item Name')
                            ->required()
                            ->unique(ignoreRecord:true)
                            ->maxLength(255),
                Forms\Components\Toggle::make('status')->inline(false)
                    ->required()->hiddenOn('create'),
                ])->columns(1)->columnSpan(2)->aside()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction(null)
        ->recordUrl(' ')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            DistributionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistributionItems::route('/'),
            'create' => Pages\CreateDistributionItem::route('/create'),
            'edit' => Pages\EditDistributionItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}