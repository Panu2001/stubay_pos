<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class LowStockAlert extends Widget
{
    protected static ?int $sort = -1;
    protected static bool $isLazy = false;
    protected string $view = 'filament.widgets.low-stock-alert';

    public function mount(): void
    {
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_notification')->get();

        if ($lowStockProducts->count() > 0) {
            Notification::make()
                ->title('Low Stock Alert!')
                ->danger()
                ->body("There are {$lowStockProducts->count()} products currently at or below their low stock threshold.")
                ->persistent()
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(route('filament.admin.resources.products.index'))
                ])
                ->send();
        }
    }
}
