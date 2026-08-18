<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashEntry extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'reason', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
