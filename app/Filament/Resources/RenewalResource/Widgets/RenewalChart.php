<?php

namespace App\Filament\Resources\RenewalResource\Widgets;

use App\Models\Renewal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Arr;

class RenewalChart extends ChartWidget
{
    protected static ?string $heading = 'Renewal Period Chart';

  protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
   // protected static ?int $sort = 0;
    protected static ?string $maxHeight = '200px';
    public function getDescription(): ?string
    {
        return 'The number of renewals per year.';
    }
    protected function getData(): array
    {
       $data = Renewal::selectRaw('Count(*) as renewal,period')
                            ->groupBy('period')
                            ->orderBy('period','asc')->pluck('period','renewal')->toArray();
       //dd($data->toArray());

       $dataLabels = array_values($data);
       $dataValues = array_keys($data);
       //dd($dataValues);
        return [
            'datasets' => [
                [
                    'label' => 'Renewals',
                    'data' => $dataValues,
                   // 'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
           // 'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'labels' => $dataLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }


}
