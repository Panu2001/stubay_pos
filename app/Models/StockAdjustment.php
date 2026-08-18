<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class StockAdjustment extends Model
{
    protected $guarded = [];

    public function stockDelta(): int
    {
        $quantity = (int) $this->quantity;

        return match ($this->type) {
            'restock', 'return' => $quantity,
            'damage', 'return_to_supplier' => -$quantity,
            'adjustment' => $quantity,
            'replace' => 0,
            default => 0,
        };
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function targetStockBatch()
    {
        return $this->belongsTo(ProductStockBatch::class, 'target_stock_batch_id');
    }

    public function stockBatch()
    {
        return $this->hasOne(ProductStockBatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::saving(function ($adjustment) {
            $quantity = (int) $adjustment->quantity;
            $type = $adjustment->type;

            if ($type === 'adjustment') {
                if ($quantity === 0) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Manual adjustment quantity cannot be zero.',
                    ]);
                }

                return;
            }

            if ($quantity <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity must be greater than zero.',
                ]);
            }

            if (in_array($type, ['damage', 'return_to_supplier'], true)) {
                $product = $adjustment->product;
                if ($product && $quantity > (int) $product->stock_quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Quantity cannot be greater than total current stock.',
                    ]);
                }

                if ($adjustment->target_stock_batch_id) {
                    $batch = ProductStockBatch::find($adjustment->target_stock_batch_id);
                    if ($batch && $quantity > (int) $batch->remaining_quantity) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Quantity cannot be greater than the specific batch\'s current stock.',
                        ]);
                    }
                }
            }
        });

        static::created(function ($adjustment) {
            $product = $adjustment->product;
            if (!$product) return;

            $adjustment->applyStockDelta($product, $adjustment->stockDelta());

            if ($adjustment->type === 'restock' && (
                $adjustment->new_price !== null ||
                $adjustment->new_cost_price !== null ||
                $adjustment->new_expiry_date !== null
            )) {
                ProductStockBatch::create([
                    'product_id' => $product->id,
                    'stock_adjustment_id' => $adjustment->id,
                    'quantity' => (int) $adjustment->quantity,
                    'remaining_quantity' => (int) $adjustment->quantity,
                    'price' => $adjustment->new_price ?? $product->price,
                    'cost_price' => $adjustment->new_cost_price ?? $product->cost_price,
                    'expiry_date' => $adjustment->new_expiry_date,
                ]);
            }
        });

        static::updated(function ($adjustment) {
            $originalProductId = $adjustment->getOriginal('product_id');
            $newProduct = $adjustment->product;
            $oldProduct = Product::find($originalProductId);

            if ($oldProduct) {
                $originalAdjustment = new self([
                    'type' => $adjustment->getOriginal('type'),
                    'quantity' => $adjustment->getOriginal('quantity'),
                    'target_stock_batch_id' => $adjustment->getOriginal('target_stock_batch_id'),
                ]);

                $adjustment->applyStockDelta($oldProduct, -$originalAdjustment->stockDelta(), $originalAdjustment->target_stock_batch_id);
            }

            if ($newProduct) {
                $adjustment->applyStockDelta($newProduct, $adjustment->stockDelta(), $adjustment->target_stock_batch_id);
            }
        });

        static::deleting(function ($adjustment) {
            $batch = $adjustment->stockBatch;

            if (! $batch) {
                return;
            }

            if ((int) $batch->remaining_quantity !== (int) $batch->quantity) {
                throw ValidationException::withMessages([
                    'stock_adjustment' => 'This restock cannot be deleted because some of its stock has already been sold.',
                ]);
            }

            $batch->delete();
        });

        static::deleted(function ($adjustment) {
            $product = $adjustment->product;
            if (!$product) return;

            $adjustment->applyStockDelta($product, -$adjustment->stockDelta(), $adjustment->target_stock_batch_id);
        });
    }

    private function applyStockDelta(Product $product, int $delta, ?int $targetBatchId = null): void
    {
        if ($delta === 0) {
            return;
        }

        if ($product->stock_quantity === null) {
            $product->stock_quantity = 0;
            $product->saveQuietly(); // Use saveQuietly to prevent triggering updated events
        }

        $product->increment('stock_quantity', $delta);

        $batchIdToUpdate = $targetBatchId ?? $this->target_stock_batch_id;
        if ($batchIdToUpdate) {
            $batch = ProductStockBatch::find($batchIdToUpdate);
            if ($batch) {
                $batch->increment('remaining_quantity', $delta);
            }
        }
        
        $product->checkAndMergeStockBatches();
    }
}
