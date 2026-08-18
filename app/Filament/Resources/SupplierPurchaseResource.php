<?php

namespace App\Filament\Resources;

use App\Models\SupplierPurchase;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class SupplierPurchaseResource extends Resource
{
    protected static ?string $model = SupplierPurchase::class;

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
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
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
                            ->native(false)
                            ->disabled(fn ($context) => $context === 'edit'),
                        TextInput::make('paid_amount')
                            ->numeric()
                            ->default(0)
                            ->label('Amount Paid')
                            ->prefix(Setting::get('currency', '$'))
                            ->required()
                            ->disabled(fn ($context) => $context === 'edit'),
                    ]),

                Repeater::make('items')
                    ->relationship()
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
                            ->prefix(Setting::get('currency', '$')),
                    ])
                    ->columns(3)
                    ->minItems(1)
                    ->defaultItems(1)
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->placeholder('Purchase notes/delivery details...')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchase_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total Cost')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . ' ' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . ' ' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('owed_amount')
                    ->label('Owed (Debt)')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . ' ' . number_format($state, 2))
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
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
                TextColumn::make('payment_method')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->label('Supplier')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('payPurchase')
                    ->label('Pay Purchase')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->label('Payment Amount')
                            ->prefix(Setting::get('currency', '$'))
                            ->maxValue(fn (SupplierPurchase $record) => $record->current_owed)
                            ->default(fn (SupplierPurchase $record) => $record->current_owed),
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'bank' => 'Bank Transfer',
                                'card' => 'Card',
                            ])
                            ->default('cash')
                            ->required()
                            ->native(false),
                        Textarea::make('notes')
                            ->placeholder('Payment notes...'),
                    ])
                    ->action(function (SupplierPurchase $record, array $data) {
                        \App\Models\SupplierPayment::create([
                            'supplier_id' => $record->supplier_id,
                            'supplier_purchase_id' => $record->id,
                            'user_id' => auth()->id(),
                            'amount' => $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'notes' => $data['notes'],
                        ]);

                        // Update columns on the purchase record directly
                        $record->increment('paid_amount', $data['amount']);
                        $record->decrement('owed_amount', $data['amount']);

                        // Log CashEntry if paid in cash
                        if ($data['payment_method'] === 'cash') {
                            \App\Models\CashEntry::create([
                                'type' => 'out',
                                'amount' => $data['amount'],
                                'reason' => "Supplier Purchase Payment: {$record->purchase_number}",
                                'notes' => $data['notes'],
                                'user_id' => auth()->id(),
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Payment Recorded')
                            ->body("Payment of " . Setting::get('currency', '$') . number_format($data['amount'], 2) . " for purchase {$record->purchase_number} successfully saved.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (SupplierPurchase $record) => $record->current_owed > 0),
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (SupplierPurchase $record) => route('supplier-purchase.receipt', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SupplierPurchaseResource\Pages\ManageSupplierPurchases::route('/'),
        ];
    }
}
