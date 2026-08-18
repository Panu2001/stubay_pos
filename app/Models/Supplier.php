<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $guarded = [];

    public function purchases(): HasMany
    {
        return $this->hasMany(SupplierPurchase::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function getBalanceAttribute()
    {
        return (float) $this->purchases()->sum('owed_amount');
    }

    public function getSettlementStatusAttribute()
    {
        $totalOwed = (float) $this->purchases()->sum('owed_amount');
        $totalPurchased = (float) $this->purchases()->sum('total_amount');

        if ($totalPurchased <= 0) {
            return 'settled';
        }

        if ($totalOwed <= 0) {
            return 'settled';
        }

        if ($totalOwed < $totalPurchased) {
            return 'partially_settled';
        }

        return 'not_settled';
    }
}
