<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currency = \App\Models\Setting::get('currency', '$');
        $customerId = auth()->id();

        $totalSpend = \App\Models\Order::where('customer_id', $customerId)->sum('total');
        
        $totalLends = \App\Models\Lend::where('customer_id', $customerId)
            ->where('status', '!=', 'paid')
            ->get()
            ->sum(fn ($lend) => $lend->remaining_amount);

        $totalOrders = \App\Models\Order::where('customer_id', $customerId)->count();

        $neededThings = \App\Models\OrderItem::whereHas('order', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })->sum('quantity');

        return [
            Stat::make('Total Spend', $currency . number_format($totalSpend, 2))
                ->description('Total amount spent on completed orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            
            Stat::make('Outstanding Lends', $currency . number_format($totalLends, 2))
                ->description('Total remaining balance to pay')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color($totalLends > 0 ? 'danger' : 'success'),

            Stat::make('Recent Orders', $totalOrders)
                ->description('Total orders placed')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Items Purchased', $neededThings)
                ->description('Total items (Needed Things) bought')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
        ];
    }
}
