<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class RevenueTrend extends ChartWidget
{
    protected ?string $heading = 'Revenue Trend';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    use \Filament\Widgets\Concerns\InteractsWithPageFilters;

    public static function canView(): bool
    {
        return !auth()->user()->isStockKeeper();
    }

    protected function getData(): array
    {
        $period = $this->filters['filter_period'] ?? 'all';

        $start = now()->startOfDay();
        $end = now()->endOfDay();
        $per = 'hour';

        switch ($period) {
            case 'today':
                $start = now()->startOfDay();
                $end = now()->endOfDay();
                $per = 'hour';
                break;
            case 'week':
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $per = 'day';
                break;
            case 'month':
                $start = now()->startOfMonth();
                $end = now()->endOfMonth();
                $per = 'day';
                break;
            case 'year':
                $start = now()->startOfYear();
                $end = now()->endOfYear();
                $per = 'month';
                break;
            case 'all':
                $start = now()->subYear();
                $end = now();
                $per = 'month';
                break;
        }

        // We don't have Trend package installed likely, so we'll do manual grouping
        $data = $this->getRevenueData($start, $end, $per);

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data['values'],
                    'fill' => 'start',
                    'tension' => 0.4,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderColor' => '#f59e0b',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getRevenueData($start, $end, $per)
    {
        $records = Order::whereBetween('created_at', [$start, $end])
            ->selectRaw("SUM(total) as total, created_at")
            ->groupBy('created_at')
            ->get();

        $labels = [];
        $values = [];

        if ($per === 'hour') {
            for ($i = 0; $i <= 23; $i++) {
                $hour = $start->copy()->addHours($i);
                $labels[] = $hour->format('H:00');
                $values[] = Order::whereBetween('created_at', [$hour->startOfHour()->toDateTimeString(), $hour->endOfHour()->toDateTimeString()])->sum('total');
            }
        } elseif ($per === 'day') {
            $days = $start->diffInDays($end);
            for ($i = 0; $i <= $days; $i++) {
                $day = $start->copy()->addDays($i);
                $labels[] = $day->format('M d');
                $values[] = Order::whereDate('created_at', $day->toDateString())->sum('total');
            }
        } elseif ($per === 'month') {
            for ($i = 0; $i < 12; $i++) {
                $month = $start->copy()->addMonths($i);
                $labels[] = $month->format('M Y');
                $values[] = Order::whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->sum('total');
            }
        }

        return ['labels' => $labels, 'values' => $values];
    }

    protected function getType(): string
    {
        return 'line';
    }
}