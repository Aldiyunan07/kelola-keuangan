<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon as SupportCarbon;

class BIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Pemasukan';

    protected static string $color = 'success';

    protected function getData(): array
    {

        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            SupportCarbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            SupportCarbon::parse($this->filters['endDate']) :
            null;

        $data = Trend::query(Transaction::incomes())
            ->between(
                start: (isset($this->filter['startDate']) !== false) ? now()->startOfYear() : $startDate,
                end: (isset($this->filter['endDate']) !== false) ? now()->endOfYear() : $endDate,
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Income Graphic',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
