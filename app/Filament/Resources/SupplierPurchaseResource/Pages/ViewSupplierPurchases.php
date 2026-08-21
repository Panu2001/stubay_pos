<?php

namespace App\Filament\Resources\SupplierPurchaseResource\Pages;

use App\Filament\Resources\SupplierPurchaseResource;
use App\Models\CashEntry;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierPurchase;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class ViewSupplierPurchases extends ViewRecord
{
    protected static string $resource = SupplierPurchaseResource::class;

    protected static ?string $title = 'Supplier Purchases';

    public function getTitle(): string
    {
        return $this->getRecord()->name . ' - Purchases';
    }

    protected function getHeaderActions(): array
    {
        $supplier = $this->getRecord();
        $currency = Setting::get('currency', '$');
        $balance = (float) $supplier->balance;

        return [
            Action::make('back')
                ->label('All Suppliers')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(SupplierPurchaseResource::getUrl('index')),

            Action::make('create_purchase')
                ->label('New Purchase')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->modalHeading("New Purchase for {$supplier->name}")
                ->modalWidth('4xl')
                ->form([
                    Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'bank' => 'Bank Transfer',
                            'card' => 'Card',
                            'credit' => 'Credit (Full Debt)',
                        ])
                        ->default('cash')
                        ->required()
                        ->native(false)
                        ->reactive(),

                    TextInput::make('paid_amount')
                        ->numeric()
                        ->default(0)
                        ->label('Amount Paid')
                        ->prefix($currency)
                        ->required(),

                    Repeater::make('items')
                        ->label('Purchase Items')
                        ->schema([
                            Select::make('product_id')
                                ->label('Product')
                                ->required()
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $search) => \App\Models\Product::where('name', 'like', "%{$search}%")
                                    ->orWhere('barcode', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($product) => [$product->id => "{$product->name} ({$product->barcode})"])
                                )
                                ->getOptionLabelUsing(fn ($value): ?string => ($p = \App\Models\Product::find($value)) ? "{$p->name} ({$p->barcode})" : null),
                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required(),
                            TextInput::make('unit_cost')
                                ->numeric()
                                ->required()
                                ->label('Cost Price')
                                ->prefix($currency),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->defaultItems(1)
                        ->columnSpanFull(),

                    Textarea::make('notes')
                        ->placeholder('Purchase notes/delivery details...')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) use ($supplier) {
                    $items = $data['items'] ?? [];
                    $total = 0;
                    foreach ($items as $item) {
                        $total += ((float)$item['quantity'] * (float)$item['unit_cost']);
                    }

                    $paid = (float)($data['paid_amount'] ?? 0);
                    if ($data['payment_method'] === 'credit') {
                        $paid = 0;
                    }

                    $owed = max(0, $total - $paid);

                    $purchase = SupplierPurchase::create([
                        'supplier_id' => $supplier->id,
                        'user_id' => auth()->id(),
                        'total_amount' => $total,
                        'paid_amount' => $paid,
                        'owed_amount' => $owed,
                        'payment_method' => $data['payment_method'],
                        'notes' => $data['notes'] ?? null,
                    ]);

                    foreach ($items as $item) {
                        $purchase->items()->create([
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_cost' => $item['unit_cost'],
                            'subtotal' => (float)$item['quantity'] * (float)$item['unit_cost'],
                        ]);
                    }

                    if ($paid > 0) {
                        SupplierPayment::create([
                            'supplier_id' => $supplier->id,
                            'supplier_purchase_id' => $purchase->id,
                            'user_id' => auth()->id(),
                            'amount' => $paid,
                            'payment_method' => $data['payment_method'],
                            'notes' => "Initial payment for purchase {$purchase->purchase_number}",
                        ]);

                        if ($data['payment_method'] === 'cash') {
                            CashEntry::create([
                                'type' => 'out',
                                'amount' => $paid,
                                'reason' => "Supplier Purchase: {$purchase->purchase_number}",
                                'notes' => $purchase->notes,
                                'user_id' => auth()->id(),
                            ]);
                        }
                    }

                    Notification::make()
                        ->title('Purchase Created')
                        ->body("Purchase {$purchase->purchase_number} saved successfully.")
                        ->success()
                        ->send();

                    $this->dispatch('print-cash-receipt', url: route('supplier-purchase.receipt', $purchase));
                }),

            Action::make('pay_supplier')
                ->label('Pay Supplier')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn () => $this->getRecord()->balance > 0)
                ->form([
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->label('Payment Amount')
                        ->prefix($currency)
                        ->maxValue(fn () => $this->getRecord()->balance)
                        ->default(fn () => $this->getRecord()->balance),
                    Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'bank' => 'Bank Transfer',
                            'card' => 'Card',
                        ])
                        ->default('cash')
                        ->required(),
                    Textarea::make('notes')
                        ->placeholder('E.g. Paid installment, clear balance...'),
                ])
                ->action(function (array $data) use ($currency) {
                    $supplier = $this->getRecord();
                    $paymentAmount = (float)$data['amount'];
                    $remaining = $paymentAmount;

                    $purchases = $supplier->purchases()
                        ->where('owed_amount', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    foreach ($purchases as $purchase) {
                        if ($remaining <= 0) {
                            break;
                        }

                        $owed = (float)$purchase->owed_amount;
                        $allocation = min($remaining, $owed);

                        SupplierPayment::create([
                            'supplier_id' => $supplier->id,
                            'supplier_purchase_id' => $purchase->id,
                            'user_id' => auth()->id(),
                            'amount' => $allocation,
                            'payment_method' => $data['payment_method'],
                            'notes' => $data['notes'] . " (Allocated from supplier payment)",
                        ]);

                        $purchase->increment('paid_amount', $allocation);
                        $purchase->decrement('owed_amount', $allocation);

                        $remaining -= $allocation;
                    }

                    if ($remaining > 0) {
                        SupplierPayment::create([
                            'supplier_id' => $supplier->id,
                            'supplier_purchase_id' => null,
                            'user_id' => auth()->id(),
                            'amount' => $remaining,
                            'payment_method' => $data['payment_method'],
                            'notes' => $data['notes'] . " (Unallocated remainder)",
                        ]);
                    }

                    if ($data['payment_method'] === 'cash') {
                        CashEntry::create([
                            'type' => 'out',
                            'amount' => $paymentAmount,
                            'reason' => "Supplier Payment to: {$supplier->name}",
                            'notes' => $data['notes'],
                            'user_id' => auth()->id(),
                        ]);
                    }

                    Notification::make()
                        ->title('Payment Recorded')
                        ->body("Payment of " . $currency . number_format($paymentAmount, 2) . " successfully saved.")
                        ->success()
                        ->send();
                }),

            Action::make('pay_specific_purchase')
                ->label('Pay Invoice')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->visible(false), // Accessed dynamically if needed
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.resources.supplier-purchases.supplier-purchases-overview')
                    ->viewData(fn (): array => $this->getSupplierPurchasesOverviewData()),
            ]);
    }

    public function deletePurchase(int $purchaseId): void
    {
        $purchase = SupplierPurchase::where('supplier_id', $this->getRecord()->id)->find($purchaseId);
        if ($purchase) {
            $purchaseNumber = $purchase->purchase_number;
            $purchase->delete();

            Notification::make()
                ->title('Purchase Deleted')
                ->body("Purchase {$purchaseNumber} has been removed and stock updated.")
                ->success()
                ->send();
        }
    }

    public function paySinglePurchase(int $purchaseId, float $amount, string $paymentMethod, ?string $notes = null): void
    {
        $purchase = SupplierPurchase::where('supplier_id', $this->getRecord()->id)->find($purchaseId);
        if (!$purchase || $purchase->current_owed <= 0) {
            return;
        }

        $amount = min($amount, $purchase->current_owed);

        SupplierPayment::create([
            'supplier_id' => $purchase->supplier_id,
            'supplier_purchase_id' => $purchase->id,
            'user_id' => auth()->id(),
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'notes' => $notes ?: "Payment for {$purchase->purchase_number}",
        ]);

        $purchase->increment('paid_amount', $amount);
        $purchase->decrement('owed_amount', $amount);

        if ($paymentMethod === 'cash') {
            CashEntry::create([
                'type' => 'out',
                'amount' => $amount,
                'reason' => "Supplier Purchase Payment: {$purchase->purchase_number}",
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);
        }

        Notification::make()
            ->title('Payment Recorded')
            ->body("Paid " . Setting::get('currency', '$') . number_format($amount, 2) . " for {$purchase->purchase_number}")
            ->success()
            ->send();
    }

    private function getSupplierPurchasesOverviewData(): array
    {
        $supplier = $this->getRecord();
        $purchases = $supplier
            ->purchases()
            ->with(['items.product', 'payments'])
            ->orderByDesc('created_at')
            ->get();

        $totalPurchases = 0;
        $totalPaid = 0;
        $outstanding = 0;
        $openPurchases = 0;

        foreach ($purchases as $purchase) {
            $totalPurchases += (float) $purchase->total_amount;
            $totalPaid += (float) $purchase->paid_amount;
            $outstanding += (float) $purchase->owed_amount;

            if ($purchase->owed_amount > 0) {
                $openPurchases++;
            }
        }

        $latestPayment = $supplier->payments()->latest()->first();

        return [
            'supplier' => $supplier,
            'currency' => Setting::get('currency', '$'),
            'purchases' => $purchases,
            'openPurchases' => $purchases->filter(fn ($p) => (float)$p->owed_amount > 0)->values(),
            'latestPayment' => $latestPayment,
            'stats' => [
                'total_purchases' => $totalPurchases,
                'total_paid' => $totalPaid,
                'outstanding' => $outstanding,
                'open_purchases' => $openPurchases,
                'purchase_count' => $purchases->count(),
                'progress' => $totalPurchases > 0 ? min(100, round(($totalPaid / $totalPurchases) * 100)) : 0,
            ],
        ];
    }
}
