<?php

namespace App\Filament\Region\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Actions\ExportAction;
use App\Filament\Exports\UserExporter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Region\Resources\UserResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\UserResource\Widgets\MembershipStatsOverview;
use App\Filament\Region\Resources\UserResource\Widgets\RegionalMembershipOverview;

class ManageUsers extends ManageRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->label('Export Members')
            ->modalHeading('Export Members')
            ->icon('heroicon-o-arrow-down-on-square-stack')
            ->chunkSize(100)
                ->exporter(UserExporter::class)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_admin', false)
                ->where('region_id', auth()->user()?->region_id))

        ];
    }

    protected  function getHeaderWidgets(): array
    {
        return [
            RegionalMembershipOverview::class,
        ];

    }
}