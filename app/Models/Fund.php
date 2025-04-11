<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fund extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'starting_balance',
        'currency',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starting_balance' => 'decimal:2',
    ];

    public function returns(): HasMany
    {
        return $this->hasMany(FundReturn::class)->orderBy('date');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(FundValueSnapshot::class)->orderBy('date');
    }

    public function currentValue(): float
    {
        $lastReturn = $this->returns()
            ->where('reverted', false)
            ->latest('date')
            ->first();

        return $lastReturn ? $lastReturn->value_after : $this->starting_balance;
    }

    public function getCurrentValueAttribute(): float
    {
        return $this->currentValue();
    }
}