<?php

namespace App\Filament\Region\Resources\TrainingResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\Column;
use Filament\Resources\Components\Tab;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Filament\Resources\RelationManagers\RelationManager;

class TrainingRegistrationsRelationManager extends RelationManager
{
    public function getTabs(): array
{
    return [
        'all' => Tab::make(),
        'present' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('attended', true)),
        'absent' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('attended', false)),
    ];
}
    protected static bool $isLazy = false;
    protected static string $relationship = 'trainingRegistrations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('member.name')
            ->recordUrl(' ')
            ->modifyQueryUsing(function($query){
                return $query->with('training','member');
            })
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('member.name')->label('Name'),
                Tables\Columns\TextColumn::make('member.staff_id')->label('Staff ID'),
                Tables\Columns\TextColumn::make('member.telephone')->label('Telephone'),
                Tables\Columns\TextColumn::make('member.gender')->label('Gender'),
            ])
            ->filters([
                //
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
                    $filename = 'registration_list_' . now()->format('ymdHis') . '.csv';

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
                            ->with($relationshipNames);
                           // ->orderBy('member.name');

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
            ->actions([
                Tables\Actions\Action::make('Attended')->label('Present')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('success')
                    ->action(function($record){
                        $record->update([
                            'attended'=>true
                        ]);
                         return Notification::make()->title('System Response')
                            ->body('Attendance saved successfully!')
                            ->info()
                            ->send();
                    })->hidden(function($record){
                        return $record->attended;
                    }),
                Tables\Actions\Action::make('Clear')->label('Clear')
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-arrow-left-start-on-rectangle')
                ->action(function($record){
                        $record->update([
                            'attended'=>false
                        ]);
                        return Notification::make()->title('System Response')
                            ->body('Attendance cleared successfully!')
                            ->info()
                            ->send();
                    })->visible(function($record){
                        return $record->attended;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                   // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
