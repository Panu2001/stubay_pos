<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Revenue Trend (Last 30 Days)';
    protected ?string $maxHeight = '400px';
    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        return !auth()->user()->isStockKeeper();
    }

    protected function getData(): array
    {
        $data = collect(range(29, 0))->mapWithKeys(function ($days) {
            $date = Carbon::now()->subDays($days)->format('Y-m-d');
            $sum = Order::whereDate('created_at', $date)->sum('total');
            return [Carbon::now()->subDays($days)->format('M d') => $sum];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Total Revenue',
                    'data' => $data->values()->toArray(),
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#fbbf24',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
