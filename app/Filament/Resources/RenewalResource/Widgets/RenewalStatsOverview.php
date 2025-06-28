<?php

namespace App\Filament\Resources\RenewalResource\Widgets;

use App\Filament\Resources\RenewalResource\Pages\ManageRenewals;
use App\Traits\NumberFormatTrait;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RenewalStatsOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?int $sort = -1;

    protected function getTablePage(): string
    {
        return ManageRenewals::class;
    }
    protected function getStats(): array
    {
        $totalRenewals = $this->getPageTableQuery()->count();

        return [
            Stat::make('Total Renewals', number_format($totalRenewals))
            ->description('This is the total number of renewals')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
        /* Stat::make('Bounce rate', '21%')
            ->description('7% increase')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger'),
        Stat::make('Average time on page', '3:12')
            ->description('3% increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'), */
        ];
    }
}
