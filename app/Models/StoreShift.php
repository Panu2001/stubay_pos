<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreShift extends Model
{
    protected $fillable = ['user_id', 'opened_at', 'closed_at', 'starting_cash', 'ending_cash', 'status'];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
