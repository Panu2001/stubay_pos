<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierPurchase extends Model
{
    protected $guarded = [];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierPurchaseItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    protected static function booted()
    {
        static::creating(function ($purchase) {
            if (!$purchase->purchase_number) {
                $purchase->purchase_number = 'PUR-' . strtoupper(uniqid());
            }
        });

        static::deleting(function ($purchase) {
            // Explicitly delete items via Eloquent to trigger their stock-reversal events
            foreach ($purchase->items as $item) {
                $item->delete();
            }
        });
    }

    public function getCurrentOwedAttribute()
    {
        return (float) $this->owed_amount;
    }

    public function getSettlementStatusAttribute()
    {
        if ($this->owed_amount <= 0) {
            return 'settled';
        }

        if ($this->paid_amount > 0) {
            return 'partially_settled';
        }

        return 'not_settled';
    }

    public function recalculateAmounts()
    {
        $total = $this->items()->sum(\Illuminate\Support\Facades\DB::raw('quantity * unit_cost'));
        $this->total_amount = $total;
        $this->owed_amount = max(0, $total - $this->paid_amount);
        $this->saveQuietly();
    }
}
