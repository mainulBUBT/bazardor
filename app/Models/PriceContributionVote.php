<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class PriceContributionVote extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'price_contribution_id',
        'user_id',
        'is_upvote',
    ];

    protected $casts = [
        'price_contribution_id' => 'string',
        'user_id' => 'string',
        'is_upvote' => 'boolean',
    ];

    /**
     * Get the price contribution that owns the vote.
     */
    public function priceContribution(): BelongsTo
    {
        return $this->belongsTo(PriceContribution::class);
    }

    /**
     * Get the user who made the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
