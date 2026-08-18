<?php

namespace App\Filament\Resources\SupplierPurchaseResource\Pages;

use App\Filament\Resources\SupplierPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSupplierPurchases extends ManageRecords
{
    protected static string $resource = SupplierPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    
                    // Calculate total and owed amounts from items array
                    $items = $data['items'] ?? [];
                    $total = 0;
                    foreach ($items as $item) {
                        $total += ((float)$item['quantity'] * (float)$item['unit_cost']);
                    }
                    $data['total_amount'] = $total;
                    
                    if ($data['payment_method'] === 'credit') {
                        $data['paid_amount'] = 0;
                    }
                    
                    $data['owed_amount'] = max(0, $total - (float)$data['paid_amount']);
                    return $data;
                })
                ->after(function (\App\Models\SupplierPurchase $record) {
                    // 1. Create supplier payment log if paid_amount > 0
                    if ($record->paid_amount > 0) {
                        \App\Models\SupplierPayment::create([
                            'supplier_id' => $record->supplier_id,
                            'supplier_purchase_id' => $record->id,
                            'user_id' => auth()->id(),
                            'amount' => $record->paid_amount,
                            'payment_method' => $record->payment_method,
                            'notes' => "Initial payment for purchase {$record->purchase_number}",
                        ]);

                        // 2. Log CashEntry if paid in cash
                        if ($record->payment_method === 'cash') {
                            \App\Models\CashEntry::create([
                                'type' => 'out',
                                'amount' => $record->paid_amount,
                                'reason' => "Supplier Purchase: {$record->purchase_number}",
                                'notes' => $record->notes,
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }

                    // 3. Trigger print receipt automatically!
                    $this->dispatch('print-cash-receipt', url: route('supplier-purchase.receipt', $record));
                }),
        ];
    }
}
