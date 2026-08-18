<?php

namespace App\Services;

use App\Models\CashEntry;
use App\Models\Order;
use App\Models\Setting;
use App\Models\StoreShift;
use App\Models\User;

class ShiftService
{
    public static function currentStoreDrawer(): array
    {
        $currency = Setting::get('currency', '$');
        $baseAmount = (float) Setting::get('cash_drawer_base_amount', 0);
        $baseAt = Setting::get('cash_drawer_base_at');
        $from = $baseAt ? \Carbon\Carbon::parse($baseAt) : now()->startOfDay();

        $cashSales = (float) Order::where('payment_method', 'cash')
            ->where('created_at', '>=', $from)
            ->sum('total');

        $cashIn = (float) CashEntry::where('type', 'in')
            ->where('created_at', '>=', $from)
            ->sum('amount');

        $cashOut = (float) CashEntry::where('type', 'out')
            ->where('created_at', '>=', $from)
            ->sum('amount');

        return [
            'currency' => $currency,
            'from' => $from,
            'base' => $baseAmount,
            'cash_sales' => $cashSales,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'current' => $baseAmount + $cashSales + $cashIn - $cashOut,
        ];
    }

    public static function setStoreDrawer(User $user, float $targetAmount): void
    {
        $targetAmount = max(0, $targetAmount);

        Setting::set('cash_drawer_base_amount', number_format($targetAmount, 2, '.', ''));
        Setting::set('cash_drawer_base_at', now()->toDateTimeString());
    }

    public static function startForUser(User $user): StoreShift
    {
        $activeShift = StoreShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if ($activeShift) {
            return $activeShift;
        }

        $lastShift = StoreShift::where('user_id', $user->id)
            ->where('status', 'closed')
            ->latest('closed_at')
            ->first();

        return StoreShift::create([
            'user_id' => $user->id,
            'opened_at' => now(),
            'starting_cash' => (float) ($lastShift?->ending_cash ?? 0),
            'status' => 'open',
        ]);
    }

    public static function closeForUser(User $user): ?StoreShift
    {
        $activeShift = StoreShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (! $activeShift) {
            return null;
        }

        $cashSales = Order::where('user_id', $user->id)
            ->where('payment_method', 'cash')
            ->where('created_at', '>=', $activeShift->opened_at)
            ->sum('total');

        $cashIn = CashEntry::where('user_id', $user->id)
            ->where('type', 'in')
            ->where('created_at', '>=', $activeShift->opened_at)
            ->sum('amount');

        $cashOut = CashEntry::where('user_id', $user->id)
            ->where('type', 'out')
            ->where('created_at', '>=', $activeShift->opened_at)
            ->sum('amount');

        $activeShift->update([
            'closed_at' => now(),
            'ending_cash' => (float) $activeShift->starting_cash + (float) $cashSales + (float) $cashIn - (float) $cashOut,
            'status' => 'closed',
        ]);

        return $activeShift->fresh();
    }
}
