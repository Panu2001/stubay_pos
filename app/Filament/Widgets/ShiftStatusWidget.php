<?php

namespace App\Filament\Widgets;

use App\Models\StoreShift;
use App\Models\Order;
use App\Models\Setting;
use App\Services\ShiftService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShiftStatusWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -1; // Show it at the very top
    protected ?string $pollingInterval = '3s';

    protected function getCachedStats(): array
    {
        return $this->getStats();
    }


    public static function canView(): bool
    {
        return !auth()->user()->isStockKeeper();
    }

    protected function getStats(): array
    {
        $currency = Setting::get('currency', '$');
        
        // Always calculate today's global sales first
        $todayTotalSales = Order::whereDate('created_at', now())->sum('total');
        $todayCashSales = Order::whereDate('created_at', now())
            ->where('payment_method', 'cash')
            ->sum('total');

        $activeShift = StoreShift::where('status', 'open')->latest('opened_at')->first();
        $drawer = ShiftService::currentStoreDrawer();

        $stats = [];

        if (!$activeShift) {
            $stats[] = Stat::make('Current Shift', 'No Active Shift')
                ->description('Total Sales Today: ' . $currency . number_format($todayTotalSales, 2))
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color('gray')
                ->url(\App\Filament\Pages\PosTerminal::getUrl());
            
            $stats[] = Stat::make('Current Drawer Cash', 'Shift Closed')
                ->description('Shared drawer: ' . $currency . number_format($drawer['current'], 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray');
        } else {
            $stats[] = Stat::make('Shift Active', 'Since ' . $activeShift->opened_at->format('H:i'))
                ->description('Shared store drawer is active')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success');

            $stats[] = Stat::make('Current Drawer Cash', $currency . number_format($drawer['current'], 2))
                ->description('Cash sales: ' . $currency . number_format($drawer['cash_sales'], 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info');
        }

        // Always show the Today's Total as the third card
        $stats[] = Stat::make('Today\'s Total Sales', $currency . number_format($todayTotalSales, 2))
            ->description('Total revenue from all shifts today')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success');

        return $stats;
    }
}
