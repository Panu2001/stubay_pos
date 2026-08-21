<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierPurchaseResource\Pages;
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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierPurchaseResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'supplier-purchases';

    protected static ?string $navigationLabel = 'Supplier Purchases';

    protected static ?string $modelLabel = 'Supplier';

    protected static ?string $pluralModelLabel = 'Supplier Purchases';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static \UnitEnum|string|null $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 8;

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return auth()->check();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isManager());
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isManager());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        $currency = Setting::get('currency', '$');

        return $table
            ->query(
                Supplier::query()->withCount('purchases')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Supplier Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn (Supplier $record): string => static::getUrl('view_purchases', ['record' => $record])),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('purchases_count')
                    ->label('Invoices')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('total_purchased')
                    ->label('Total Purchased')
                    ->getStateUsing(fn (Supplier $record) => $record->purchases()->sum('total_amount'))
                    ->formatStateUsing(fn ($state) => $currency . ' ' . number_format((float)$state, 2))
                    ->sortable(false),

                TextColumn::make('paid_amount')
                    ->label('Total Paid')
                    ->getStateUsing(fn (Supplier $record) => $record->purchases()->sum('paid_amount'))
                    ->formatStateUsing(fn ($state) => $currency . ' ' . number_format((float)$state, 2))
                    ->color('success')
                    ->sortable(false),

                TextColumn::make('balance')
                    ->label('Balance Owed')
                    ->formatStateUsing(fn ($state) => $currency . ' ' . number_format((float)$state, 2))
                    ->badge()
                    ->color(fn ($state) => (float)$state > 0 ? 'danger' : 'success')
                    ->sortable(false),

                TextColumn::make('settlement_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'settled' => 'success',
                        'partially_settled' => 'warning',
                        'not_settled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'settled' => 'Settled',
                        'partially_settled' => 'Partially Settled',
                        'not_settled' => 'Not Settled',
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('has_balance')
                    ->label('With Outstanding Debt')
                    ->query(fn ($query) => $query->whereHas('purchases', fn ($q) => $q->where('owed_amount', '>', 0))),
            ])
            ->actions([
                Action::make('view_purchases')
                    ->label('View Purchases')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn (Supplier $record): string => static::getUrl('view_purchases', ['record' => $record])),

                Action::make('new_purchase')
                    ->label('New Purchase')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(fn (Supplier $record) => "New Purchase for {$record->name}")
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
                            ->native(false),

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
                    ->action(function (Supplier $record, array $data) {
                        static::executeCreatePurchase($record->id, $data);
                    }),

                Action::make('paySupplier')
                    ->label('Pay Balance')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->visible(fn (Supplier $record) => (float)$record->balance > 0)
                    ->form([
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->label('Payment Amount')
                            ->prefix($currency)
                            ->maxValue(fn (Supplier $record) => $record->balance)
                            ->default(fn (Supplier $record) => $record->balance),
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
                    ->action(function (Supplier $record, array $data) use ($currency) {
                        $paymentAmount = (float)$data['amount'];
                        $remaining = $paymentAmount;

                        $purchases = $record->purchases()
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
                                'supplier_id' => $record->id,
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
                                'supplier_id' => $record->id,
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
                                'reason' => "Supplier Payment to: {$record->name}",
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
            ]);
    }

    public static function getCreatePurchaseAction(): Action
    {
        $currency = Setting::get('currency', '$');

        return Action::make('create_purchase')
            ->label('New Purchase')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->modalHeading('New Supplier Purchase')
            ->modalWidth('4xl')
            ->form([
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('purchases.supplier', 'name')
                    ->options(fn () => Supplier::pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank' => 'Bank Transfer',
                        'card' => 'Card',
                        'credit' => 'Credit (Full Debt)',
                    ])
                    ->default('cash')
                    ->required()
                    ->native(false),

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
            ->action(function (array $data) {
                static::executeCreatePurchase((int)$data['supplier_id'], $data);
            });
    }

    public static function executeCreatePurchase(int $supplierId, array $data): SupplierPurchase
    {
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
            'supplier_id' => $supplierId,
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
                'supplier_id' => $supplierId,
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

        return $purchase;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplierPurchases::route('/'),
            'view_purchases' => Pages\ViewSupplierPurchases::route('/{record}/purchases'),
        ];
    }
}
