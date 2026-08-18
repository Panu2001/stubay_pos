<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lend extends Model
{
    protected $fillable = [
        'customer_id',
        'order_id',
        'total_amount',
        'paid_amount',
        'status',
        'due_date',
        'notes',
    ];

    protected static function booted()
    {
        static::saving(function ($lend) {
            $lend->total_amount = round(max(0, (float) $lend->total_amount), 2);
            $lend->paid_amount = round(max(0, (float) $lend->paid_amount), 2);

            if ($lend->paid_amount > $lend->total_amount) {
                $lend->paid_amount = $lend->total_amount;
            }

            $total = (float) $lend->total_amount;
            $paid = (float) $lend->paid_amount;

            if ($paid >= $total) {
                $lend->status = 'paid';
            } elseif ($paid > 0) {
                $lend->status = 'partially_paid';
            } else {
                $lend->status = 'unpaid';
            }
        });

        static::created(function ($lend) {
            \App\Jobs\PushSalesToCloudJob::dispatch();
        });
    }

    public function getRemainingAmountAttribute(): float
    {
        return round(max(0, (float) $this->total_amount - (float) $this->paid_amount), 2);
    }

    public function applyPaymentCorrection(float $totalAmount, float $paidAmount, ?string $dueDate, ?string $notes): void
    {
        $totalAmount = round(max(0, $totalAmount), 2);
        $paidAmount = round(max(0, $paidAmount), 2);

        if ($paidAmount > $totalAmount) {
            throw new \InvalidArgumentException('Paid amount cannot be greater than the lend amount.');
        }

        $this->forceFill([
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'due_date' => $dueDate,
            'notes' => $notes,
        ])->save();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
