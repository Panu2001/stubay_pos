<?php

namespace App\Filament\Resources;

use App\Models\Supplier;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static \UnitEnum|string|null $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 7;

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
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('contact_person')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Textarea::make('address')
                    ->placeholder('Supplier office address...')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Supplier $record): string => SupplierPurchaseResource::getUrl('view_purchases', ['record' => $record])),
                TextColumn::make('contact_person')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('balance')
                    ->label('Balance Owed')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . ' ' . number_format($state, 2))
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
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
                    ->label('Outstanding Balance')
                    ->query(fn ($query) => $query->whereHas('purchases', function ($q) {
                        // Keep simple query or custom check if needed, otherwise filter by suppliers with balance > 0
                    })), // We can leave it basic or skip
            ])
            ->actions([
                Action::make('makePayment')
                    ->label('Pay Supplier')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->label('Payment Amount')
                            ->prefix(Setting::get('currency', '$'))
                            ->maxValue(fn (Supplier $record) => $record->balance)
                            ->default(fn (Supplier $record) => $record->balance),
                        \Filament\Forms\Components\Select::make('payment_method')
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
                    ->action(function (Supplier $record, array $data) {
                        $paymentAmount = (float)$data['amount'];
                        $remaining = $paymentAmount;

                        // Find all purchases that have outstanding balance
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

                            // Create a payment record for this specific purchase
                            \App\Models\SupplierPayment::create([
                                'supplier_id' => $record->id,
                                'supplier_purchase_id' => $purchase->id,
                                'user_id' => auth()->id(),
                                'amount' => $allocation,
                                'payment_method' => $data['payment_method'],
                                'notes' => $data['notes'] . " (Allocated from general supplier payment)",
                            ]);

                            // Update the purchase record columns
                            $purchase->increment('paid_amount', $allocation);
                            $purchase->decrement('owed_amount', $allocation);

                            $remaining -= $allocation;
                        }

                        // If there is still remaining payment (should not happen if capped, but as a fallback)
                        if ($remaining > 0) {
                            \App\Models\SupplierPayment::create([
                                'supplier_id' => $record->id,
                                'supplier_purchase_id' => null,
                                'user_id' => auth()->id(),
                                'amount' => $remaining,
                                'payment_method' => $data['payment_method'],
                                'notes' => $data['notes'] . " (Unallocated remainder)",
                            ]);
                        }

                        // Log CashEntry if paid by cash
                        if ($data['payment_method'] === 'cash') {
                            \App\Models\CashEntry::create([
                                'type' => 'out',
                                'amount' => $paymentAmount,
                                'reason' => "Supplier Payment to: {$record->name}",
                                'notes' => $data['notes'],
                                'user_id' => auth()->id(),
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Payment Recorded')
                            ->body("Payment of " . Setting::get('currency', '$') . number_format($paymentAmount, 2) . " to {$record->name} successfully saved and allocated to outstanding purchases.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Supplier $record) => $record->balance > 0),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SupplierResource\Pages\ManageSuppliers::route('/'),
        ];
    }
}
