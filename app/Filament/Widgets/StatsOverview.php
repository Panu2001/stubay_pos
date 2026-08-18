<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Lend;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    use \Filament\Widgets\Concerns\InteractsWithPageFilters;

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return !auth()->user()->isStockKeeper();
    }

    protected function getStats(): array
    {
        $currency = Setting::get('currency', '$');
        $period = $this->filters['filter_period'] ?? 'all';

        $query = Order::query();
        
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', now());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
            case 'all':
            default:
                // Do not apply any date constraints to show all-time data
                break;
        }

        $totalRevenue = (clone $query)->sum('total');
        $ordersCount = (clone $query)->count();
        $customersCount = Customer::count(); // Usually total customers doesn't filter
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_notification')->count();
        
        $lendQuery = Lend::query();
        switch ($period) {
            case 'today':
                $lendQuery->whereDate('created_at', now());
                break;
            case 'week':
                $lendQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $lendQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                break;
            case 'year':
                $lendQuery->whereYear('created_at', now()->year);
                break;
            case 'all':
            default:
                // Do not apply any date constraints to show all-time data
                break;
        }
        $totalDebt = (clone $lendQuery)->get()->sum(fn ($lend) => $lend->remaining_amount);

        $totalTax = (clone $query)->sum('tax');
        
        // Use the historical total_profit calculated at checkout
        $totalProfit = (clone $query)->sum('total_profit');
        
        $expiringCount = Product::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();
            
        $returnAlertCount = Product::whereNotNull('return_notification_date')
            ->where('return_notification_date', '<=', now())
            ->count();

        return [
            Stat::make('Total Revenue', $currency . number_format($totalRevenue, 2))
                ->description('Total earnings from orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
            Stat::make('Total Profit', $currency . number_format($totalProfit, 2))
                ->description('Revenue minus tax and item costs')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
            Stat::make('Total Orders', $ordersCount)
                ->description('Total completed transactions')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
            Stat::make('Total Lends (Debt)', $currency . number_format($totalDebt, 2))
                ->description('Outstanding customer credit')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color($totalDebt > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Resources\Lends\Lends\LendResource::getUrl()),
            Stat::make('POS Terminal', 'Open Terminal')
                ->description('Start a new sale')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('primary')
                ->url(\App\Filament\Pages\PosTerminal::getUrl()),
            Stat::make('Customers', $customersCount)
                ->description('Total registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->url(\App\Filament\Resources\Customers\CustomerResource::getUrl()),
            Stat::make('Low Stock Items', $lowStockCount)
                ->description('Products needing restock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Resources\Products\ProductResource::getUrl('index', ['tableFilters' => ['low_stock' => ['isActive' => 1]]])),
            Stat::make('Expiring Items', $expiringCount)
                ->description('Expiring within 30 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color($expiringCount > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Resources\Products\ProductResource::getUrl('index', ['tableFilters' => ['expiring_soon' => ['isActive' => 1]]])),
            Stat::make('Return Alerts', $returnAlertCount)
                ->description('Products due for return')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($returnAlertCount > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Resources\Products\ProductResource::getUrl()),
        ];
    }
}
