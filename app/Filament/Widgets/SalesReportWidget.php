<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class SalesReportWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getStats(): array
    {
        $orders = Order::query()
            ->when($this->startDate, fn ($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('created_at', '<=', $this->endDate));

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        
        // Basic profit calculation: (Sale Price - Cost Price) * Quantity
        // This is a bit complex with standard SQL if we want it to be exact per order item,
        // but we can approximate or join.
        $totalProfit = $orders->sum('total_profit');

        $inventoryValue = Product::query()->selectRaw('SUM(stock_quantity * cost_price) as value')->value('value') ?? 0;

        return [
            Stat::make('Total Sales', 'Rs. ' . number_format($totalSales, 2))
                ->description('Total revenue in selected period')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
            Stat::make('Total Profit', 'Rs. ' . number_format($totalProfit, 2))
                ->description('Estimated profit (Revenue - Cost)')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
            Stat::make('Total Orders', $totalOrders)
                ->description('Number of completed transactions')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
            Stat::make('Inventory Value', 'Rs. ' . number_format($inventoryValue, 2))
                ->description('Current value of all stock (at cost)')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('warning')
                ->url(\App\Filament\Resources\Products\ProductResource::getUrl()),
        ];
    }
}
