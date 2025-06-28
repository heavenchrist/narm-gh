<?php

namespace App\Filament\Resources;

use export;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Region;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Contribution;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ContributionResource\Pages;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Filament\Resources\ContributionResource\RelationManagers;
use App\Filament\Resources\ContributionResource\Widgets\ContributionStatsOverview;

class ContributionResource extends Resource
{
    protected static ?string $model = Contribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('staff_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('place_of_work')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('district')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('region')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('period')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(' ')
        ->modifyQueryUsing(function($query){
            //add relationship member
            return $query->with('member:id,name','user:id,name','member.region:id,name');
        })
            ->columns([
                Tables\Columns\TextColumn::make('member.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('staff_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.region.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    //->numeric(decimalPlaces: 2)
                    ->money('GHS',locale: 'en')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
            Tables\Actions\Action::make('download')
                ->modalHeading('Download Records')
                ->requiresConfirmation()
                ->color(Color::Emerald)
                ->modalDescription(function (HasTable $livewire) {
                    $count = $livewire->getFilteredTableQuery()->count();
                    $strText = Str::plural('record',$count);

                    return new HtmlString("You are about to download <b>".number_format($count)." {$strText}</b>. <br/>Do you want to continue?");
                })
                ->modalSubmitActionLabel('Yes')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (HasTable $livewire): StreamedResponse {
                    $filename = 'contributions_' . now()->format('ymdHis') . '.csv';

                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"$filename\"",
                    ];

                    return Response::stream(function () use ($livewire) {
                        // Prevent timeouts
                        ini_set('max_execution_time', 0);  // Unlimited
                        ini_set('memory_limit', -1);       // Unlimited

                        $handle = fopen('php://output', 'w');

                        // Get visible columns
                        $columns = collect($livewire->getTable()->getColumns())
                            ->filter(fn (Column $column) => $column->isVisible())
                            ->values();

                        // Write CSV header
                        fputcsv($handle, $columns->map(fn (Column $col) => $col->getLabel())->all());

                        // Detect relationship names for eager loading
                        $relationshipNames = $columns
                            ->map(fn ($col) => $col->getName())
                            ->filter(fn ($name) => str_contains($name, '.'))
                            ->map(fn ($name) => explode('.', $name)[0])
                            ->unique()
                            ->all();

                        // Get base query (filtered, sorted)
                        $query = $livewire->getFilteredTableQuery()
                            ->with($relationshipNames)
                            ->orderBy('period', 'desc');

                        // Process in chunks
                        $query->chunk(100, function ($chunk) use ($handle, $columns) {
                            foreach ($chunk as $record) {
                                $row = $columns->map(function (Column $column) use ($record) {
                                    $columnName = $column->getName(); // e.g. 'user.name' or 'name'
                                    return Arr::get($record->toArray(), $columnName);
                                });

                                fputcsv($handle, $row->all());

                                // Flush output to avoid memory build-up
                                flush();
                            }
                        });

                        fclose($handle);
                    }, 200, $headers);
                }),
        ])
            ->filters([
               /*  Filter::make('filter')
                ->form([
                    Select::make('region')->options(Contribution::distinct()->select('region as name','region as id')->pluck('name','id')),
                    Select::make('period')->options(Contribution::distinct()->selectRaw('CONCAT(MONTHNAME(period)," ,",YEAR(period)) as name,period as id')->pluck('name','id')),
                    //DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {

                    return $query
                        ->when(
                            $data['region'],
                            fn (Builder $query, $region): Builder => $query->where('region', '=', $region),
                        )
                        ->when(
                            $data['period'],
                            fn (Builder $query, $period): Builder => $query->whereDate('period', '<=', $period),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                            if ($data['region'] ?? null) {
                                $indicators[] = Indicator::make('Region Name: ' . $data['region'])
                                    ->removeField('region');
                            }

                            if ($data['period'] ?? null) {
                                $indicators[] = Indicator::make('Period ' . Carbon::parse($data['period'])->toFormattedDateString())
                                    ->removeField('period');
                            }

                            return $indicators;
                        }) */

            ])->deferFilters()
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::Large)
                ->modalSubmitActionLabel('Save')
                ->modalIcon('heroicon-o-pencil-square')
                ->slideOver(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageContributions::route('/'),
        ];
    }

    //widget
    public static function getWidgets(): array
    {
        return [
            ContributionStatsOverview::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}