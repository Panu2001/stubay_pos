<?php

namespace App\Filament\Resources\StockAdjustments;

use App\Filament\Resources\StockAdjustments\Pages\ManageStockAdjustments;
use App\Models\StockAdjustment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';
    protected static \UnitEnum|string|null $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdmin() || $user->isManager() || $user->isCashier() || $user->isStockKeeper());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable(['name', 'barcode'])
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Product $record) => "{$record->name} ({$record->barcode})")
                    ->live()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
                        'restock' => 'New Stock (Restock)',
                        'return' => 'Customer Return',
                        'damage' => 'Damaged/Expired',
                        'adjustment' => 'Manual Adjustment',
                        'replace' => 'Stock Replace (No Total Change)',
                        'return_to_supplier' => 'Return to Supplier',
                    ])
                    ->required()
                    ->default('restock')
                    ->live()
                    ->native(false),
                Select::make('target_stock_batch_id')
                    ->label('Which Stock to Adjust?')
                    ->options(function (\Filament\Schemas\Components\Utilities\Get $get) {
                        $productId = $get('product_id');
                        if (!$productId) {
                            return [];
                        }

                        $product = \App\Models\Product::with('activeStockBatches')->find($productId);
                        if (!$product) {
                            return [];
                        }

                        // Calculate old stock quantity
                        $batchStock = $product->activeStockBatches->sum('remaining_quantity');
                        $oldStock = max(0, (int) ($product->stock_quantity ?? 0) - $batchStock);

                        // Old stock as the first explicit option
                        $options = [
                            'old' => "Old Stock (Available: {$oldStock})",
                        ];

                        // New stock batches
                        foreach ($product->activeStockBatches as $batch) {
                            $label = "New Stock";
                            if ($batch->expiry_date) {
                                $label .= " - Exp " . $batch->expiry_date->format('Y-m-d');
                            }
                            $label .= " (Available: {$batch->remaining_quantity})";
                            $options[$batch->id] = $label;
                        }

                        return $options;
                    })
                    ->default('old')
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => $state === 'old' ? null : $state)
                    ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('product_id') && $get('type') !== 'restock')
                    ->native(false),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->helperText('Use a positive number for normal stock changes. Manual adjustment may be negative to subtract stock.')
                    ->rules([
                        function ($get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $quantity = (int) $value;
                                $type = $get('type');

                                if ($type === 'adjustment') {
                                    if ($quantity === 0) {
                                        $fail('Manual adjustment quantity cannot be zero.');
                                    }

                                    return;
                                }

                                if ($quantity <= 0) {
                                    $fail('Quantity must be greater than zero.');

                                    return;
                                }

                                if (in_array($type, ['damage', 'return_to_supplier'], true)) {
                                    $productId = $get('product_id');
                                    $product = $productId ? \App\Models\Product::find($productId) : null;

                                    if ($product && $quantity > (int) $product->stock_quantity) {
                                        $fail('Quantity cannot be greater than current stock.');
                                    }
                                }
                            };
                        },
                    ]),
                TextInput::make('new_price')
                    ->label('New Stock Sell Price')
                    ->numeric()
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->visible(fn ($get) => $get('type') === 'restock')
                    ->helperText('Optional. Use only if this restock must sell at a different price.'),
                TextInput::make('new_cost_price')
                    ->label('New Stock Cost Price')
                    ->numeric()
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->visible(fn ($get) => $get('type') === 'restock')
                    ->helperText('Optional. Used for profit calculation for this batch.'),
                DatePicker::make('new_expiry_date')
                    ->label('New Stock Expiry Date')
                    ->visible(fn ($get) => $get('type') === 'restock')
                    ->helperText('Optional. Keeps this restock separate from old stock.'),
                Textarea::make('notes')
                    ->placeholder('Reason for adjustment...')
                    ->columnSpanFull(),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('product.name')
                    ->label('Product'),
                TextEntry::make('user.name')
                    ->label('Adjusted By'),
                TextEntry::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'restock' => 'success',
                        'return' => 'info',
                        'damage' => 'danger',
                        'adjustment' => 'warning',
                        'replace' => 'gray',
                        'return_to_supplier' => 'danger',
                        default => 'secondary',
                    }),
                TextEntry::make('targetStockBatch.id')
                    ->label('Targeted Stock')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'restock') return 'New Batch created/added';
                        if (!$record->target_stock_batch_id) return 'Old Stock';
                        
                        $batch = $record->targetStockBatch;
                        if (!$batch) return 'Old Stock';
                        
                        return "New Stock" . ($batch->expiry_date ? " (Exp: {$batch->expiry_date->format('Y-m-d')})" : "");
                    }),
                TextEntry::make('quantity')
                    ->formatStateUsing(fn ($state) => (string) $state),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Date')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(['name', 'barcode'])
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('By')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'restock' => 'success',
                        'return' => 'info',
                        'damage' => 'danger',
                        'adjustment' => 'warning',
                        'replace' => 'gray',
                        'return_to_supplier' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'restock' => 'Restock',
                        'return' => 'Return',
                        'damage' => 'Damage',
                        'adjustment' => 'Adjustment',
                        'replace' => 'Replace',
                        'return_to_supplier' => 'Return to Supplier',
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('quantity')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => match ($record->type) {
                        'damage', 'return_to_supplier' => "-$state",
                        'replace' => "= $state (Replace)",
                        default => "+$state",
                    }),
                TextColumn::make('targetStockBatch.expiry_date')
                    ->label('Targeted Stock')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'restock') return 'New Batch created';
                        if (!$record->target_stock_batch_id) return 'Old Stock';
                        
                        $batch = $record->targetStockBatch;
                        if (!$batch) return 'Old Stock';
                        
                        return "New Stock" . ($batch->expiry_date ? " (Exp: {$batch->expiry_date->format('Y-m-d')})" : "");
                    }),
                TextColumn::make('new_price')
                    ->label('New Price')
                    ->formatStateUsing(fn ($state) => $state === null ? '-' : \App\Models\Setting::get('currency', '$') . number_format($state, 2))
                    ->toggleable(),
                TextColumn::make('new_expiry_date')
                    ->label('New Expiry')
                    ->date()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('created_until')->label('To Date'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'restock' => 'Restock',
                        'return' => 'Return',
                        'damage' => 'Damage',
                        'adjustment' => 'Adjustment',
                        'replace' => 'Replace',
                        'return_to_supplier' => 'Return to Supplier',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable(['name', 'barcode'])
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\Product $record) => "{$record->name} ({$record->barcode})"),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete stock adjustment?')
                    ->modalDescription('This will undo the stock quantity change created by this adjustment. Restocks can only be deleted if the added batch has not been sold yet.'),
            ])
            ->toolbarActions([
                // Usually stock adjustments shouldn't be bulk deleted for audit trail
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStockAdjustments::route('/'),
        ];
    }
}
