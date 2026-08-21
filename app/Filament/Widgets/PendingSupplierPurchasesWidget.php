<?php

namespace App\Filament\Widgets;

use App\Models\SupplierPurchase;
use Filament\Widgets\Widget;

class PendingSupplierPurchasesWidget extends Widget
{
    protected string $view = 'filament.widgets.pending-supplier-purchases-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    protected function getViewData(): array
    {
        $pendingPurchases = SupplierPurchase::where('owed_amount', '>', 0)->get();
        
        $totalAmount = $pendingPurchases->sum('total_amount');
        $paidAmount = $pendingPurchases->sum('paid_amount');
        $owedAmount = $pendingPurchases->sum('owed_amount');
        
        $progress = $totalAmount > 0 ? ($paidAmount / $totalAmount) * 100 : 0;
        
        return [
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'owedAmount' => $owedAmount,
            'progress' => min(100, max(0, $progress)),
        ];
    }
}
