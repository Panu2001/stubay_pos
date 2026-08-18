<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentOrders extends TableWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Recent Sales';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return !auth()->user()->isStockKeeper();
    }

    public function table(Table $table): Table
    {
        $currency = \App\Models\Setting::get('currency', '$');

        return $table
            ->query(
                Order::query()->with('customer')->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->placeholder('Walk-in')
                    ->searchable(),
                TextColumn::make('total')
                    ->prefix($currency)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
