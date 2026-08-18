<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockBatch extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'remaining_quantity' => 'integer',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockAdjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class);
    }
}
