<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Renewal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RenewalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RenewalResource\RelationManagers;
use App\Filament\Resources\RenewalResource\Widgets\RenewalChart;
use App\Filament\Resources\RenewalResource\Widgets\RenewalStatsOverview;
use Filament\Forms\Components\Section;

class RenewalResource extends Resource
{
    protected static ?string $model = Renewal::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('staff_id')
                     ->required()
                    ->maxLength(255),
            ])->columns(1);
    }


    public static function table(Table $table): Table
    {
        return $table
        ->recordAction(null)
            ->columns([
                Tables\Columns\TextColumn::make('staff_id')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pin_ain')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('renewal_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Filter::make('filter')

                ->form([
                    Section::make()->schema([
                        Select::make('period')->options(Renewal::distinct()->selectRaw('period as name, period as id')->pluck('name','id')),
                        DatePicker::make('expiry_date')->native(false)->maxDate(now()->addYear()),
                        DatePicker::make('renewal_date')->native(false)->maxDate(now()),
                    ])->columns(3)

                ])->columnSpanFull()
                ->query(function (Builder $query, array $data): Builder {
                /* if(array_key_exists('region',$data)) {
                    if($data['region'] != null) {
                    dd($data);
                    }
                } */
                    return $query
                        ->when(
                            $data['period'],
                            fn (Builder $query, $period): Builder => $query->where('period', '=', $period),
                        )
                        ->when(
                            $data['expiry_date'],
                            fn (Builder $query, $expiry_date): Builder => $query->whereDate('expiry_date', '=', $expiry_date),
                        )
                        ->when(
                            $data['renewal_date'],
                            fn (Builder $query, $renewal_date): Builder => $query->whereDate('renewal_date', '=', $renewal_date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                            if ($data['period'] ?? null) {
                                $indicators[] = Indicator::make('Period: ' . $data['period'])
                                    ->removeField('period');
                            }

                            if ($data['expiry_date'] ?? null) {
                                $indicators[] = Indicator::make('Expiry date ' . Carbon::parse($data['expiry_date'])->toFormattedDateString())
                                    ->removeField('expiry_date');
                            }
                            if ($data['renewal_date'] ?? null) {
                                $indicators[] = Indicator::make('Renewal date ' . Carbon::parse($data['renewal_date'])->toFormattedDateString())
                                    ->removeField('renewal_date');
                            }

                            return $indicators;
                        })

            ], layout: FiltersLayout::AboveContentCollapsible)->deferFilters()->filtersFormColumns(3)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::Medium)
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
            'index' => Pages\ManageRenewals::route('/'),
        ];
    }


}
