<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentSales;
use App\Filament\Widgets\RevenueTrend;
use App\Filament\Widgets\ShiftStatusWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TimeDateWidget;
use App\Models\StoreShift;
use App\Services\ShiftService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFilters;

class Dashboard extends BaseDashboard
{
    use HasFilters;

    protected static ?string $title = 'POS Dashboard';

    public string $salesTrendPeriod = 'week';
    public array $chartData = [];
    public array $chartLabels = [];

    public string $profitTrendPeriod = 'week';
    public array $profitChartData = [];
    public array $profitChartLabels = [];

    protected string $view = 'filament.pages.dashboard';

    public function mount()
    {
        $this->updateChartData();
        $this->updateProfitChartData();
    }

    public function updatedSalesTrendPeriod()
    {
        $this->updateChartData();
    }

    public function updatedProfitTrendPeriod()
    {
        $this->updateProfitChartData();
    }

    protected function updateChartData()
    {
        $data = [];
        $labels = [];
        $period = $this->salesTrendPeriod;

        if ($period === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $startHour = now()->startOfDay()->addHours($i);
                $endHour = $startHour->copy()->endOfHour();
                $sales = \App\Models\Order::whereBetween('created_at', [$startHour, $endHour])->sum('total');
                if ($sales > 0 || $startHour->isBefore(now())) {
                    $data[] = (float) $sales;
                    $labels[] = $startHour->format('g A');
                }
            }
        } elseif ($period === 'month') {
            $daysInMonth = now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $day = now()->startOfMonth()->addDays($i - 1);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();
                $sales = \App\Models\Order::whereBetween('created_at', [$dayStart, $dayEnd])->sum('total');
                if ($sales > 0 || $dayStart->isBefore(now())) {
                    $data[] = (float) $sales;
                    $labels[] = $day->format('M j');
                }
            }
        } elseif ($period === 'year') {
            for ($i = 1; $i <= 12; $i++) {
                $month = now()->startOfYear()->addMonths($i - 1);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                $sales = \App\Models\Order::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total');
                if ($sales > 0 || $monthStart->isBefore(now())) {
                    $data[] = (float) $sales;
                    $labels[] = $month->format('M');
                }
            }
        } else {
            // week (default)
            for ($i = 6; $i >= 0; $i--) {
                $day = now()->subDays($i);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();
                $sales = \App\Models\Order::whereBetween('created_at', [$dayStart, $dayEnd])->sum('total');
                $data[] = (float) $sales;
                $labels[] = $day->format('D, M j');
            }
        }

        $this->chartData = $data;
        $this->chartLabels = $labels;
        $this->dispatch('sales-chart-updated');
    }

    protected function updateProfitChartData()
    {
        $data = [];
        $labels = [];
        $period = $this->profitTrendPeriod;

        // Helper: sum profit only for items that have a known cost price
        $profitQuery = fn ($start, $end) =>
            \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$start, $end])
                ->where('order_items.unit_cost_price', '>', 0)
                ->selectRaw('SUM((order_items.unit_price - order_items.unit_cost_price) * order_items.quantity) as profit')
                ->value('profit') ?? 0;

        if ($period === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $startHour = now()->startOfDay()->addHours($i);
                $endHour   = $startHour->copy()->endOfHour();
                $profit = $profitQuery($startHour, $endHour);
                if ($profit > 0 || $startHour->isBefore(now())) {
                    $data[]   = (float) $profit;
                    $labels[] = $startHour->format('g A');
                }
            }
        } elseif ($period === 'month') {
            $daysInMonth = now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $day      = now()->startOfMonth()->addDays($i - 1);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd   = $day->copy()->endOfDay();
                $profit   = $profitQuery($dayStart, $dayEnd);
                if ($profit > 0 || $dayStart->isBefore(now())) {
                    $data[]   = (float) $profit;
                    $labels[] = $day->format('M j');
                }
            }
        } elseif ($period === 'year') {
            for ($i = 1; $i <= 12; $i++) {
                $month      = now()->startOfYear()->addMonths($i - 1);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd   = $month->copy()->endOfMonth();
                $profit     = $profitQuery($monthStart, $monthEnd);
                if ($profit > 0 || $monthStart->isBefore(now())) {
                    $data[]   = (float) $profit;
                    $labels[] = $month->format('M');
                }
            }
        } else {
            // week (default)
            for ($i = 6; $i >= 0; $i--) {
                $day      = now()->subDays($i);
                $dayStart = $day->copy()->startOfDay();
                $dayEnd   = $day->copy()->endOfDay();
                $data[]   = (float) $profitQuery($dayStart, $dayEnd);
                $labels[] = $day->format('D, M j');
            }
        }

        $this->profitChartData   = $data;
        $this->profitChartLabels = $labels;
    }

    protected function getHeaderWidgetsFiltersFormSchema(): array
    {
        return [
            Select::make('filter_period')
                ->label('Filter Period')
                ->options([
                    'today' => 'Today',
                    'week' => 'This Week',
                    'month' => 'This Month',
                    'year' => 'This Year',
                    'all' => 'All Time',
                ])
                ->default('all')
                ->live(),
        ];
    }

    public function getHeaderWidgetsFiltersColumns(): int|array
    {
        return 3;
    }

    public function getWidgets(): array
    {
        return [
            TimeDateWidget::class,
            ShiftStatusWidget::class,
            StatsOverview::class,
            RevenueTrend::class,
            RecentSales::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    Select::make('filter_period')
                        ->label('Filter Period')
                        ->options([
                            'today' => 'Today',
                            'week' => 'This Week',
                            'month' => 'This Month',
                            'year' => 'This Year',
                            'all' => 'All Time',
                        ])
                        ->default('all'),
                ]),
            Action::make('closeShiftAndLogout')
                ->label('Close Shift & Logout')
                ->icon('heroicon-o-power')
                ->color('danger')
                ->visible(fn () => StoreShift::where('user_id', auth()->id())->where('status', 'open')->exists())
                ->requiresConfirmation()
                ->modalHeading('Close Your Shift?')
                ->modalDescription('This will close your current cash drawer using cash sales, cash-in, and cash-out entries.')
                ->action(function () {
                    $user = auth()->user();
                    ShiftService::closeForUser($user);

                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->to(filament()->getLoginUrl());
                }),
        ];
    }
}
