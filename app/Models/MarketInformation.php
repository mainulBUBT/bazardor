<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class MarketInformation extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'market_id' => 'string',
        'is_non_veg' => 'boolean',
        'is_halal' => 'boolean',
        'is_parking' => 'boolean',
        'is_restroom' => 'boolean',
        'is_home_delivery' => 'boolean',
    ];

    /**
     * Get the market that owns the information.
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }
}
