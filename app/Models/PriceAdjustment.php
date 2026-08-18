<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceAdjustment extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($adjustment) {
            $product = $adjustment->product;
            if (!$product) {
                return;
            }

            // Update Old Stock (Product base prices)
            if ($adjustment->old_stock_price_to !== null) {
                $product->price = $adjustment->old_stock_price_to;
            }
            if ($adjustment->old_stock_cost_to !== null) {
                $product->cost_price = $adjustment->old_stock_cost_to;
            }
            $product->saveQuietly(); // Use saveQuietly to prevent triggering updated loops

            // Update New Stock (Active ProductStockBatches)
            if ($adjustment->new_stock_price_to !== null || $adjustment->new_stock_cost_to !== null) {
                $activeBatches = $product->activeStockBatches;
                foreach ($activeBatches as $batch) {
                    if ($adjustment->new_stock_price_to !== null) {
                        $batch->price = $adjustment->new_stock_price_to;
                    }
                    if ($adjustment->new_stock_cost_to !== null) {
                        $batch->cost_price = $adjustment->new_stock_cost_to;
                    }
                    $batch->save();
                }
            }
        });
    }
}
