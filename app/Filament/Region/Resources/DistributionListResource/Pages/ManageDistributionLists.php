<?php

namespace App\Filament\Region\Resources\DistributionListResource\Pages;

use App\Filament\Exports\DistributionListExporter;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Support\Colors\Color;
use Filament\Resources\Components\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Region\Resources\DistributionListResource;

class ManageDistributionLists extends ManageRecords
{
    protected static string $resource = DistributionListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->label('Export')
            ->icon('heroicon-o-arrow-up-on-square-stack')
            ->exporter(DistributionListExporter::class)
            ->maxRows(100000)
            ->chunkSize(100)
        ];
    }

    public function getTabs(): array
{
    return [
        'all' => Tab::make()
        //->badgeColor('info')
        ->icon('heroicon-m-archive-box'),

        'received' => Tab::make()
        ->icon('heroicon-m-hand-thumb-up')
        //->badgeColor('success')
        //->iconPosition(IconPosition::After)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('is_received', true)),
        'not_received' => Tab::make()
        //->badgeColor(Color::Amber)
        ->icon('heroicon-m-no-symbol')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('is_received', false)),
    ];
}
}