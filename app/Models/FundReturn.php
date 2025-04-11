<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundReturn extends Model
{
    protected $fillable = [
        'fund_id',
        'date',
        'frequency',
        'return_percentage',
        'is_compounding',
        'value_before',
        'value_after',
        'reverted',
        'notes'
    ];

    protected $casts = [
        'is_compounding' => 'boolean',
        'reverted' => 'boolean',
        'return_percentage' => 'decimal:2',
        'value_before' => 'decimal:2',
        'value_after' => 'decimal:2',
        'date' => 'date',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }
}