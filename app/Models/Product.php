<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProductPriceHistory;
use App\Traits\TracksMedia;

class Product extends Model
{
    use TracksMedia;
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function stockBatches()
    {
        return $this->hasMany(ProductStockBatch::class);
    }

    public function activeStockBatches()
    {
        return $this->hasMany(ProductStockBatch::class)
            ->where('remaining_quantity', '>', 0)
            ->orderByRaw('expiry_date is null')
            ->orderBy('expiry_date')
            ->orderBy('created_at');
    }

    public function priceHistories()
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            if (!$product->barcode) {
                $prefix = $product->prefix ?? '000';
                // Generate a unique 12-digit barcode: 3-digit prefix + 9 random digits
                $product->barcode = $prefix . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
                
                // Ensure uniqueness
                while (static::where('barcode', $product->barcode)->exists()) {
                    $product->barcode = $prefix . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
                }
            }
        });

        static::updated(function ($product) {
            if ($product->wasChanged(['price', 'cost_price'])) {
                ProductPriceHistory::create([
                    'product_id' => $product->id,
                    'old_cost_price' => $product->getOriginal('cost_price') ?? 0,
                    'new_cost_price' => $product->cost_price ?? 0,
                    'old_sell_price' => $product->getOriginal('price') ?? 0,
                    'new_sell_price' => $product->price ?? 0,
                    'user_id' => auth()->id(),
                ]);
            }
        });
    }

    public function checkAndMergeStockBatches()
    {
        // Refresh to get latest DB values
        $this->refresh();
        $totalStock = (int) $this->stock_quantity;
        $batchStock = (int) $this->activeStockBatches()->sum('remaining_quantity');

        // If Old Stock is 0 or less (Total <= New), merge New into Old
        if ($totalStock <= $batchStock && $batchStock > 0) {
            $batch = $this->activeStockBatches()->first();
            
            if ($batch) {
                // Update product prices to match the new batch that is becoming old stock
                $this->updateQuietly([
                    'cost_price' => $batch->cost_price ?? $this->cost_price,
                    'price' => $batch->new_price ?? $batch->price ?? $this->price,
                ]);
            }

            // Clear all active batches (they are now the base old stock)
            foreach ($this->activeStockBatches as $b) {
                $b->updateQuietly(['remaining_quantity' => 0]);
            }
        }
    }
}
