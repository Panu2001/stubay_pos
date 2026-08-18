<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class Customer extends Authenticatable implements FilamentUser
{
    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function lends(): HasMany
    {
        return $this->hasMany(Lend::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'customer';
    }
}
