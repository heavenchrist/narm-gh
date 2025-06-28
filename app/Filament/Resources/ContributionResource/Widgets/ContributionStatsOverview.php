<?php

namespace App\Filament\Resources\ContributionResource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\ContributionResource\Pages\ManageContributions;
use Illuminate\Support\Number;

use function Filament\Support\format_money;

class ContributionStatsOverview extends BaseWidget
{
  protected static bool $isLazy = false;
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ManageContributions::class;
    }
    protected function getStats(): array
    {
        $totalAmount = ($this->getPageTableQuery()->sum('amount')/100);
        //dd($totalAmount);
        $totalAmount = Number::currency($totalAmount,'GHS',100);
        return [
            Stat::make('Total Contributions', $totalAmount)
            ->description('This is the total amount of contributions')
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
