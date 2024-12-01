<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class CExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Pengeluaran';

    protected static string $color = 'danger';

    protected function getData(): array
    {

        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $data = Trend::query(Transaction::expenses())
            ->between(
                start: (isset($this->filter['startDate']) !== false) ? now()->startOfYear() : $startDate,
                end: (isset($this->filter['endDate']) !== false) ? now()->endOfYear() : $endDate,
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Expense Graphic',
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
