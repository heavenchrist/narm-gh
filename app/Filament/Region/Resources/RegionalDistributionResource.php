<?php

namespace App\Filament\Region\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Models\RegionalDistribution;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Region\Resources\RegionalDistributionResource\Pages;
use App\Filament\Region\Resources\RegionalDistributionResource\RelationManagers;

class RegionalDistributionResource extends Resource
{
    protected static ?string $model = RegionalDistribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $modelLabel = 'Regional Distribution Items';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                 Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('distribution_item_id')
                    ->relationship('distributionItem', 'name')
                    ->required(),
                Forms\Components\Select::make('user_id')->label('Issued By')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('office_id')
                    ->relationship('office', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_received')
                    ->required(),
                Forms\Components\Textarea::make('remarks')
                    ->columnSpanFull(),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
        ->recordUrl(' ')
            ->columns([

                Tables\Columns\TextColumn::make('distributionItem.name')
                    ->numeric()

                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->description(function($record){
                        return 'Quantity :'. $record->distribution_count .'/'. $record->quantity;
                    }),
                Tables\Columns\TextColumn::make('user.name')->label('Issued By')
                     ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_received')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Assigned Date')
                    ->dateTime()
                    ->description(function($record){
                        return $record->created_at?->diffForHumans();
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('receivedBy.name')->label('Received By'),
                Tables\Columns\TextColumn::make('received_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver()
                ->visible(function($record){
                    return $record->is_received;
                }),
                Action::make('distribute')->visible(function($record){
                    return $record->is_received;
                })
                ->modalHeading("Make Distribution")
                ->icon('heroicon-s-share')
                ->modalWidth(MaxWidth::Medium)
                ->modalIcon('heroicon-o-pencil-square')->slideOver()
                //->requiresConfirmation()
                ->form([
                    Forms\Components\Select::make('member_id')->label('Member')
                    ->options(User::selectRaw('Concat(name," (",staff_id,")") as name,id')
                            ->where('region_id', '=',auth()->user()->region_id)
                            ->where('is_admin', false)
                            ->pluck('name', 'id'))
                    ->searchable(['name','staff_id'])
                    ->preload()
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('quantity')->label('Quantity')->numeric()
                ->minValue(1)->maxValue(function($record){
                    //dd($record);
                    return ($record->quantity - $record->distribution_count);
                })
                ->required(),
                ])
                ->modalSubmitActionLabel('Assign')
                    ->action(function($record, array $data){
                  //  dd($data);
                    //check if quantity is greater than distribution_count
                        // its within range save else return false
                    $record->increment('distribution_count', $data['quantity']);
                    //insert into distribution list
                    //DistributionList::
                    //dd($record->distributionItem);

                    $record->distributionLists()->create([
                        'member_id' =>$data['member_id'],
                        //'distribution_id',
                       // 'is_received',
                       // 'user_id',
                       // 'office_id',
                        'distribution_item_id' =>$record->distributionItem->id,
                        'quantity'=>$data['quantity'],
                    ]);

                    /*
                      'member_id',
                    'distribution_id',
                    'is_received',
                    'user_id',
                    'office_id',
                    'distribution_item_id',
                    'quantity',
                    'distribution_list_id',
                     */
                }),
                Action::make('receive')->hidden(function($record){
                    return $record->is_received;
                })
                ->modalHeading("Receive Distribution")
                ->icon('heroicon-s-arrow-down-right')
                ->modalWidth(MaxWidth::Medium)
                ->modalIcon('heroicon-o-pencil-square')->slideOver()
                //->requiresConfirmation()
                ->form([
                    Forms\Components\Checkbox::make('is_received')
                    ->label('I want to receive this item')
                    //->dehydrated(false)
                        ->accepted() ->validationMessages([
                            'accepted' => 'You must receive this item to submit',
                        ]),
                    MarkdownEditor::make('remarks')->columnSpanFull()->required()
                ])
                ->modalSubmitActionLabel('Receive')
                    ->action(function($record, array $data){
                    //dd($data);
                    $record->update([
                        'remarks'=> $data['remarks'],
                        'is_received'=> $data['is_received'],
                        'received_by'=> auth()->user()->id,
                        'received_date'=> now(),
                    ]);
                })
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegionalDistributions::route('/'),
            'create' => Pages\CreateRegionalDistribution::route('/create'),
            'edit' => Pages\EditRegionalDistribution::route('/{record}/edit'),
        ];
    }
}
