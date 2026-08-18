<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPurchaseItem extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->subtotal = (float)$item->quantity * (float)$item->unit_cost;
        });

        static::created(function ($item) {
            // Adjust product stock
            $product = $item->product;
            if ($product) {
                $product->increment('stock_quantity', $item->quantity);
                $product->checkAndMergeStockBatches();
            }
            
            // Recalculate parent purchase total
            if ($item->purchase) {
                $item->purchase->recalculateAmounts();
            }
        });

        static::updating(function ($item) {
            $item->subtotal = (float)$item->quantity * (float)$item->unit_cost;

            $oldProductId = $item->getOriginal('product_id');
            $oldQuantity = (float)$item->getOriginal('quantity');

            if ($item->product_id == $oldProductId) {
                $diff = (float)$item->quantity - $oldQuantity;
                if ($diff != 0) {
                    $product = $item->product;
                    if ($product) {
                        $product->increment('stock_quantity', $diff);
                        $product->checkAndMergeStockBatches();
                    }
                }
            } else {
                // Product changed: decrement old product, increment new product
                $oldProduct = \App\Models\Product::find($oldProductId);
                if ($oldProduct) {
                    $oldProduct->decrement('stock_quantity', $oldQuantity);
                    $oldProduct->checkAndMergeStockBatches();
                }
                $newProduct = $item->product;
                if ($newProduct) {
                    $newProduct->increment('stock_quantity', $item->quantity);
                    $newProduct->checkAndMergeStockBatches();
                }
            }
        });

        static::updated(function ($item) {
            // Recalculate parent purchase total
            if ($item->purchase) {
                $item->purchase->recalculateAmounts();
            }
        });

        static::deleted(function ($item) {
            // Adjust product stock (decrement)
            $product = $item->product;
            if ($product) {
                $product->decrement('stock_quantity', $item->quantity);
                $product->checkAndMergeStockBatches();
            }

            // Recalculate parent purchase total
            if ($item->purchase) {
                $item->purchase->recalculateAmounts();
            }
        });
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(SupplierPurchase::class, 'supplier_purchase_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
