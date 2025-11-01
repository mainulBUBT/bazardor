<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class MarketOperatingHour extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'market_id',
        'day',
        'opening',
        'closing',
        'is_closed',
    ];

    protected $casts = [
        'market_id' => 'string',
        'is_closed' => 'boolean',
    ];

    /**
     * Get the market that owns the operating hour.
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }
}
