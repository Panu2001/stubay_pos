<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];
    protected $appends = ['name'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productStockBatch(): BelongsTo
    {
        return $this->belongsTo(ProductStockBatch::class);
    }

    public function getNameAttribute(): string
    {
        return $this->custom_name ?: ($this->product ? $this->product->name : 'Deleted Product');
    }
}
