<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Traits\NumberFormatTrait;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class MembershipStatsOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListUsers::class;
    }
    protected function getStats(): array
    {
        $totalMembership = $this->getPageTableQuery()->count();

        return [
            Stat::make('Total Members', NumberFormatTrait::short($totalMembership))
            ->description('This is the total number of members')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('info'),
         Stat::make('Online', '21%')
            ->description('7% increase')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('success'),
        Stat::make('Offline', '3:12')
            ->description('3% increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('danger'), /**/
        ];
    }
}
