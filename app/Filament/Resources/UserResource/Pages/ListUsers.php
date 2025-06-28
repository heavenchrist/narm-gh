<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Actions\ExportAction;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\UserResource\Widgets\MembershipStatsOverview;

class ListUsers extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->label('Export Members')->modalHeading('Export Members')
            ->icon('heroicon-o-arrow-down-on-square-stack')
            ->chunkSize(100)
                ->exporter(UserExporter::class)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_admin', false))
        ];
    }

    protected  function getHeaderWidgets(): array
    {
        return [
            MembershipStatsOverview::class,
        ];

    }
}
