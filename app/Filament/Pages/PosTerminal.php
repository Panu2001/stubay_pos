<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Jobs\PushSalesToCloudJob;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Lend;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductStockBatch;
use App\Models\Setting;
use App\Models\StoreShift;
use App\Services\ShiftService;
use App\Models\Tax;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class PosTerminal extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-computer-desktop';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'POS TERMINAL';

    protected string $view = 'filament.pages.pos-terminal';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user->isAdmin() || $user->isManager() || $user->isCashier();
    }

    public $completedOrderId = null;

    public $lastPaymentMethod = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageProducts')
                ->label('Add New Product')
                ->icon('heroicon-o-plus')
                ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isManager())
                ->url(fn () => ProductResource::getUrl('create')),

            Action::make('quickAddProduct')
                ->label('Quick Add Product')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->form([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->label('Product Name')
                                ->placeholder('e.g. Loose Sugar 1kg')
                                ->columnSpanFull(),
                            TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->label('Selling Price')
                                ->prefix(Setting::get('currency', '$')),
                            TextInput::make('cost_price')
                                ->required()
                                ->numeric()
                                ->label('Cost Price')
                                ->prefix(Setting::get('currency', '$')),
                            Select::make('category_id')
                                ->options(Category::whereNull('parent_id')->pluck('name', 'id'))
                                ->label('Category')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->default(fn () => Category::whereNull('parent_id')->first()?->id),
                            TextInput::make('unit_quantity')
                                ->label('Quantity (Size)')
                                ->required()
                                ->numeric()
                                ->default(1),
                            Select::make('unit')
                                ->options([
                                    'kg' => 'Kilogram (kg)',
                                    'g' => 'Gram (g)',
                                    'ml' => 'Milliliter (ml)',
                                    'l' => 'Liter (l)',
                                    'piece' => 'Piece',
                                    'box' => 'Box',
                                    'pkt' => 'Packet',
                                    'bottle' => 'Bottle',
                                ])
                                ->label('Unit')
                                ->required()
                                ->searchable(),
                            TextInput::make('stock_quantity')
                                ->required()
                                ->numeric()
                                ->label('Initial Stock')
                                ->default(100),
                        ]),
                ])
                ->action(function (array $data) {
                    $product = Product::create([
                        'name' => $data['name'],
                        'price' => $data['price'],
                        'cost_price' => $data['cost_price'],
                        'category_id' => $data['category_id'],
                        'stock_quantity' => $data['stock_quantity'],
                        'unit_quantity' => $data['unit_quantity'],
                        'unit' => $data['unit'],
                        'barcode' => 'QUICK-'.strtoupper(uniqid()),
                    ]);

                    $this->dispatch('product-quick-added', product: [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'unit' => $product->unit,
                    ]);

                    Notification::make()
                        ->title('Product Created & Added')
                        ->body("{$product->name} has been added to your inventory and cart.")
                        ->success()
                        ->send();
                }),

            Action::make('manageCategories')
                ->label('Categories')
                ->icon('heroicon-o-tag')
                ->color('gray')
                ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isManager())
                ->url(fn () => CategoryResource::getUrl('index')),

            Action::make('closeShift')
                ->label('Close Shift & Logout')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->visible(fn () => StoreShift::where('user_id', auth()->id())->where('status', 'open')->exists())
                ->requiresConfirmation()
                ->modalHeading('Close Your Shift?')
                ->modalDescription('This will close your current cash drawer using cash sales, cash-in, and cash-out entries.')
                ->action(function () {
                    $user = auth()->user();
                    ShiftService::closeForUser($user);

                    auth()->logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->to(filament()->getLoginUrl());
                }),
        ];
    }

    // UI State
    public $search = '';

    public $activeCategoryId = null;

    public $activeSubcategoryId = null;

    public $perPage = 36;

    // Quick Custom Sale Calculator State
    public $calculatorPrice = '';

    public $calculatorName = '';

    // Cart State
    public $cart = [];

    public $taxRate = 0;

    public $currency = '$';

    public $customerId = null;

    public $discount = 0;

    // Calculated values for syncing
    public $subtotal = 0;

    public $tax = 0;

    public $total = 0;

    public function updatedCart()
    {
        $this->calculateTotals();
    }

    public function updatedDiscount()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $this->discount = max(0, min((float) $this->discount, $this->subtotal));
        $this->tax = $this->subtotal * $this->taxRate;
        $this->total = max(0, $this->subtotal + $this->tax - $this->discount);

        $this->dispatch('sync-customer-display', [
            'cart' => array_values($this->cart),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => 'shopping',
        ]);
    }

    public function appendCalculatorPrice($val)
    {
        // Don't allow multiple decimals
        if ($val === '.' && str_contains($this->calculatorPrice, '.')) {
            return;
        }

        // Prevent typing more than two decimal points after the dot
        if (str_contains($this->calculatorPrice, '.')) {
            $parts = explode('.', $this->calculatorPrice);
            if (isset($parts[1]) && strlen($parts[1]) >= 2) {
                return;
            }
        }

        $this->calculatorPrice .= $val;
    }

    public function clearCalculator()
    {
        $this->calculatorPrice = '';
        $this->calculatorName = '';
    }

    public function addCalculatorItem($price = null)
    {
        if ($price !== null) {
            $this->calculatorPrice = $price;
        }

        $price = (float) $this->calculatorPrice;
        if ($price <= 0) {
            $this->posAlert('Invalid price. Please enter an amount greater than 0.');

            return;
        }

        $name = trim($this->calculatorName) ?: 'Custom Item';
        $customId = 'custom_'.uniqid();

        $this->cart[$customId] = [
            'id' => $customId,
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'unit' => 'piece',
            'is_custom' => true,
        ];

        // Reset
        $this->calculatorPrice = '';
        $this->calculatorName = '';

        $this->calculateTotals();

        $this->posAlert("{$name} has been added to your cart.");
    }

    public function mount()
    {
        session()->forget(['filament.notifications', 'filament.claimed_notifications']);

        // Fetch dynamic currency
        $this->currency = Setting::get('currency', '$');

        // Fetch default tax rate
        $defaultTax = Tax::where('is_active', true)->where('is_default', true)->first();
        if ($defaultTax) {
            $this->taxRate = $defaultTax->rate / 100;
        } else {
            // Fallback to settings
            $this->taxRate = (float) Setting::get('tax_rate', 5) / 100;
        }

        $this->calculateTotals();
    }

    public function getCategoriesProperty()
    {
        return Category::whereNull('parent_id')->get();
    }

    public function getSubcategoriesProperty()
    {
        if (! $this->activeCategoryId) {
            return collect();
        }

        return Category::where('parent_id', $this->activeCategoryId)->get();
    }

    public function getCustomersProperty()
    {
        return Customer::all();
    }

    public function getProductsProperty()
    {
        return Product::query()
            ->with('activeStockBatches')
            ->when(! $this->search && $this->activeSubcategoryId, function ($q) {
                return $q->where('subcategory_id', $this->activeSubcategoryId);
            })
            ->when(! $this->search && $this->activeCategoryId && ! $this->activeSubcategoryId, function ($q) {
                return $q->where('category_id', $this->activeCategoryId);
            })
            ->when($this->search, function ($q) {
                $search = $this->search;

                return $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->limit($this->perPage + 1)
            ->get();
    }

    public function loadMore()
    {
        $this->perPage += 36;
    }

    public function updatedSearch()
    {
        $this->perPage = 36;
    }

    public function setCategory($id)
    {
        $this->activeCategoryId = $id;
        $this->activeSubcategoryId = null;
        $this->perPage = 36;
    }

    public function setSubcategory($id)
    {
        $this->activeSubcategoryId = $id;
        $this->perPage = 36;
    }

    public function scanBarcode($barcode)
    {
        Log::info('[POS Scanner] scanBarcode called with: "'.$barcode.'"');
        $searchQuery = trim(preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $barcode));
        if (empty($searchQuery)) {
            Log::info('[POS Scanner] search query is empty after trim.');

            return null;
        }

        // Try exact barcode match first
        $product = Product::with('activeStockBatches')->where('barcode', $searchQuery)->first();

        // If not found, try exact name match
        if (! $product) {
            $product = Product::with('activeStockBatches')->where('name', $searchQuery)->first();
        }

        if (! $product) {
            $product = Product::with('activeStockBatches')->where('name', 'like', "%{$searchQuery}%")
                ->orderByRaw('barcode = ? desc', [$searchQuery])
                ->orderByRaw('name = ? desc', [$searchQuery])
                ->first();
        }

        if ($product) {
            Log::info('[POS Scanner] Product found: '.$product->name.' (ID: '.$product->id.')');

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) ($product->price ?? 0),
                'unit' => $product->unit,
                'stock_quantity' => (int) ($product->stock_quantity ?? 0),
                'stock_options' => $this->stockOptionsForProduct($product),
            ];
        }

        Log::info('[POS Scanner] Product not found.');

        return null;
    }

    public function scanExactBarcode($barcode)
    {
        Log::info('[POS Scanner] scanExactBarcode called with: "'.$barcode.'"');
        $searchQuery = trim(preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $barcode));
        if (empty($searchQuery)) {
            return null;
        }

        $product = Product::with('activeStockBatches')->where('barcode', $searchQuery)->first();

        if (! $product) {
            Log::info('[POS Scanner] Exact barcode not found.');

            return null;
        }

        Log::info('[POS Scanner] Exact barcode product found: '.$product->name.' (ID: '.$product->id.')');

        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) ($product->price ?? 0),
            'unit' => $product->unit,
            'stock_quantity' => (int) ($product->stock_quantity ?? 0),
            'stock_options' => $this->stockOptionsForProduct($product),
        ];
    }

    protected function stockOptionsForProduct(Product $product): array
    {
        $batchTotal = $product->activeStockBatches->sum('remaining_quantity');
        $oldStockQuantity = max(0, (int) ($product->stock_quantity ?? 0) - $batchTotal);

        $options = [[
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) ($product->price ?? 0),
            'unit' => $product->unit,
            'stock_batch_id' => null,
            'stock_label' => 'Old stock',
            'stock_quantity' => $oldStockQuantity,
        ]];

        foreach ($product->activeStockBatches as $batch) {
            $options[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) ($batch->price ?? $product->price ?? 0),
                'unit' => $product->unit,
                'stock_batch_id' => $batch->id,
                'stock_label' => 'New stock' . ($batch->expiry_date ? ' - Exp ' . $batch->expiry_date->format('Y-m-d') : ''),
                'stock_quantity' => (int) $batch->remaining_quantity,
            ];
        }

        return $options;
    }

    public function notifyProductNotFound()
    {
        $this->posAlert('Product not found.');
    }

    public function notifyInvalidPrice()
    {
        $this->posAlert('Invalid price. Please enter an amount greater than 0.');
    }

    protected function posAlert(string $message): void
    {
        $this->dispatch('pos-alert', message: $message);
    }

    public function syncCart(array $cart, $discount = 0, $customerId = null)
    {
        session()->forget(['filament.notifications', 'filament.claimed_notifications']);

        $this->cart = [];
        foreach ($cart as $key => $item) {
            if (isset($item['id']) && isset($item['name']) && isset($item['price']) && isset($item['quantity'])) {
                $price = max(0, (float) $item['price']);
                $quantity = max(0, (float) $item['quantity']);
                if ($quantity <= 0) {
                    continue;
                }

                $this->cart[$key] = [
                    'id' => $item['id'],
                    'cart_key' => $item['cart_key'] ?? $key,
                    'name' => $item['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'unit' => $item['unit'] ?? 'piece',
                    'is_custom' => ! empty($item['is_custom']),
                    'stock_batch_id' => $item['stock_batch_id'] ?? null,
                    'stock_label' => $item['stock_label'] ?? 'Old stock',
                ];
            }
        }
        $this->discount = max(0, (float) $discount);
        $this->customerId = $customerId ?: null;

        $this->calculateTotals();
    }

    public function startCheckout(array $cart, $discount = 0, $customerId = null): void
    {
        $this->syncCart($cart, $discount, $customerId);

        if (empty($this->cart)) {
            $this->posAlert('Please add a product to the cart first.');

            return;
        }

        $this->mountAction('checkout');
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->discount = 0;
        $this->customerId = null;
        $this->calculateTotals();
    }

    public function getSubtotal()
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    public function getTax()
    {
        return round($this->getSubtotal() * $this->taxRate, 2);
    }

    public function getTotal()
    {
        $subtotal = $this->getSubtotal();
        $discount = max(0, min((float) $this->discount, $subtotal));

        return round(max(0, $subtotal + $this->getTax() - $discount), 2);
    }

    public function checkoutAction(): Action
    {
        return Action::make('checkout')
            ->label(fn () => 'Pay '.$this->currency.number_format($this->getTotal(), 2))
            ->modalHeading('Complete Payment')
            ->modalWidth('md')
            ->modalSubmitActionLabel('Confirm & Pay')
            ->form([
                Radio::make('payment_method')
                    ->label('Select Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'lend' => 'Lend',
                    ])
                    ->default('cash')
                    ->inline()
                    ->live()
                    ->required(),
                TextInput::make('amount_tendered')
                    ->label('Amount Tendered')
                    ->numeric()
                    ->prefix($this->currency)
                    ->visible(fn ($get) => $get('payment_method') === 'cash')
                    ->required(fn ($get) => $get('payment_method') === 'cash')
                    ->live()
                    ->afterStateUpdated(function ($set, $state) {
                        $total = $this->getTotal();
                        if ((float) $state >= $total) {
                            $set('change', number_format((float) $state - $total, 2, '.', ''));
                        } else {
                            $set('change', '0.00');
                        }
                    })
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                if ($get('payment_method') === 'cash' && (float) $value < $this->getTotal()) {
                                    $fail('Amount tendered must be at least the total order amount.');
                                }
                            };
                        },
                    ]),
                TextInput::make('change')
                    ->label('Change to Return')
                    ->numeric()
                    ->prefix($this->currency)
                    ->readonly()
                    ->dehydrated(false)
                    ->visible(fn ($get) => $get('payment_method') === 'cash'),
            ])
            ->action(function (array $data, Action $action) {
                if ($data['payment_method'] === 'lend') {
                    if (empty($this->customerId)) {
                        $this->posAlert('You must select a customer to process a lend transaction.');
                        $action->halt();
                    }

                    $newTotal = $this->getTotal();
                    $currentOutstanding = Lend::where('customer_id', $this->customerId)
                        ->where('status', '!=', 'paid')
                        ->get()
                        ->sum(fn ($lend) => $lend->remaining_amount);

                    $customer = Customer::find($this->customerId);
                    $lendLimit = $customer ? (float) ($customer->monthly_lend_limit ?? 25000) : 25000;

                    if (($currentOutstanding + $newTotal) > $lendLimit) {
                        $this->posAlert(sprintf(
                                "Outstanding Balance: %s. Current Order: %s. Combined: %s exceeds the customer's maximum limit of %s%s. The customer must pay their outstanding lends first.",
                                $this->currency . number_format($currentOutstanding, 2),
                                $this->currency . number_format($newTotal, 2),
                                $this->currency . number_format($currentOutstanding + $newTotal, 2),
                                $this->currency,
                                number_format($lendLimit, 2)
                        ));
                        $action->halt();
                    }
                }

                $amountTendered = null;
                if ($data['payment_method'] === 'cash' && isset($data['amount_tendered'])) {
                    $amountTendered = $data['amount_tendered'];
                }

                $orderId = $this->checkout($data['payment_method'], $amountTendered);
                if ($orderId) {
                    $this->completedOrderId = $orderId;
                    $this->dispatch('checkout-completed');
                }
            });
    }

    #[On('checkout-completed')]
    public function triggerPrintReceipt()
    {
        if ($this->lastPaymentMethod === 'cash') {
            $url = route('receipt', $this->completedOrderId);
            $this->dispatch('print-receipt', url: $url);

            $this->posAlert('Payment successful. Cash receipt has been sent to printer.');
        } else {
            $this->mountAction('printReceipt');
        }
    }

    public function printReceiptAction(): Action
    {
        return Action::make('printReceipt')
            ->modalHeading('Order Successfully Processed')
            ->modalDescription('The payment has been accepted and stock levels are updated. Would you like to print the receipt for the customer?')
            ->modalSubmitActionLabel('Print Receipt')
            ->modalCancelActionLabel('Next Order')
            ->modalIcon('heroicon-o-check-circle')
            ->modalIconColor('success')
            ->modalWidth('md')
            ->extraModalFooterActions(fn (): array => [
                // empty to prevent default behaviors if necessary, or just rely on submit/cancel
            ])
            ->action(function () {
                $url = route('receipt', $this->completedOrderId);
                $this->dispatch('print-receipt', url: $url);
            });
    }

    public function checkout($paymentMethod = 'cash', $amountTendered = null)
    {
        if (empty($this->cart)) {
            return null;
        }

        $createdOrderId = null;
        DB::transaction(function () use ($paymentMethod, $amountTendered, &$createdOrderId) {
            $subtotal = $this->getSubtotal();
            $tax = $this->getTax();
            $total = $this->getTotal();
            $discount = max(0, min((float) $this->discount, $subtotal));

            // Pre-load all products to prevent N+1 query performance bottleneck
            $productIds = collect($this->cart)->where('is_custom', '!=', true)->pluck('id')->unique();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            $batchIds = collect($this->cart)->where('is_custom', '!=', true)->pluck('stock_batch_id')->filter()->unique();
            $stockBatches = ProductStockBatch::whereIn('id', $batchIds)->lockForUpdate()->get()->keyBy('id');

            // Pre-calculate order-wide cost and profit
            $orderTotalCost = 0;
            foreach ($this->cart as $item) {
                if (! empty($item['is_custom'])) {
                    // For custom item, cost_price is 0, so profit = price
                    continue;
                }
                $product = $products->get($item['id']);
                $batch = ! empty($item['stock_batch_id']) ? $stockBatches->get($item['stock_batch_id']) : null;
                $costPrice = $batch ? ($batch->cost_price ?? $product->cost_price ?? 0) : ($product->cost_price ?? 0);
                $orderTotalCost += ($costPrice * $item['quantity']);
            }

            $orderTotalProfit = -$discount;
            foreach ($this->cart as $item) {
                if (! empty($item['is_custom'])) {
                    $orderTotalProfit += ($item['price'] * $item['quantity']);

                    continue;
                }
                $product = $products->get($item['id']);
                $batch = ! empty($item['stock_batch_id']) ? $stockBatches->get($item['stock_batch_id']) : null;
                $costPrice = $batch ? ($batch->cost_price ?? $product->cost_price ?? 0) : ($product->cost_price ?? 0);
                $orderTotalProfit += (($item['price'] - $costPrice) * $item['quantity']);
            }

            $change = null;
            if ($paymentMethod === 'cash' && $amountTendered !== null) {
                $change = max(0, (float) $amountTendered - $total);
            }

            $order = Order::create([
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'user_id' => auth()->id(),
                'customer_id' => $this->customerId ?: null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'tax_rate' => $this->taxRate * 100,
                'total_cost' => round($orderTotalCost, 2),
                'total_profit' => round($orderTotalProfit, 2),
                'total' => $total,
                'payment_method' => $paymentMethod,
                'amount_tendered' => $amountTendered,
                'change' => $change,
            ]);

            if ($paymentMethod === 'lend') {
                Lend::create([
                    'customer_id' => $this->customerId,
                    'order_id' => $order->id,
                    'total_amount' => $order->total,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                ]);
            }

            foreach ($this->cart as $item) {
                if (! empty($item['is_custom'])) {
                    $costPrice = 0;
                    $sellPrice = (float) $item['price'];
                    $profitPerUnit = $sellPrice;
                    $totalProfit = $profitPerUnit * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => null,
                        'custom_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_cost_price' => $costPrice,
                        'unit_price' => $sellPrice,
                        'profit_per_unit' => $profitPerUnit,
                        'total_profit' => $totalProfit,
                        'subtotal' => $sellPrice * $item['quantity'],
                    ]);
                } else {
                    $product = $products->get($item['id']);
                    $batch = ! empty($item['stock_batch_id']) ? $stockBatches->get($item['stock_batch_id']) : null;
                    $costPrice = $batch ? ($batch->cost_price ?? $product->cost_price ?? 0) : ($product->cost_price ?? 0);
                    $sellPrice = (float) $item['price'];
                    $profitPerUnit = $sellPrice - $costPrice;
                    $totalProfit = $profitPerUnit * $item['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'product_stock_batch_id' => $batch?->id,
                        'quantity' => $item['quantity'],
                        'unit_cost_price' => $costPrice,
                        'unit_price' => $sellPrice,
                        'profit_per_unit' => $profitPerUnit,
                        'total_profit' => $totalProfit,
                        'subtotal' => $sellPrice * $item['quantity'],
                    ]);

                    $product->decrement('stock_quantity', $item['quantity']);
                    if ($batch) {
                        $batch->decrement('remaining_quantity', $item['quantity']);
                    }
                }
            }

            $this->clearCart();

            $this->lastPaymentMethod = $paymentMethod;
            $createdOrderId = $order->id;
        });

        PushSalesToCloudJob::dispatch();

        return $createdOrderId;
    }
}
