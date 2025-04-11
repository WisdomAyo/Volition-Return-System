<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundValueSnapshot extends Model
{
    protected $fillable = ['fund_id', 'date', 'value'];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:2',
    ];

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }
}