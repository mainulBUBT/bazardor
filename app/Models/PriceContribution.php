<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'market_id',
        'user_id',
        'submitted_price',
        'proof_image',
        'status',
        'upvotes',
        'downvotes',
        'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'submitted_price' => 'decimal:2',
    ];

    /**
     * Get the user who submitted the price.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product this price is for.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the market this price is for.
     */
    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * Get the votes for this price contribution.
     */
    public function votes()
    {
        return $this->hasMany(PriceContributionVote::class);
    }

    /**
     * Calculate confidence score based on votes
     */
    public function getConfidenceScoreAttribute()
    {
        $n = $this->upvotes + $this->downvotes;
        if ($n === 0) return 0;
        
        $z = 1.96; // 95% confidence
        $p = $this->upvotes / $n;
        
        // Wilson score interval
        $left = $p + ($z * $z / (2 * $n));
        $right = $z * sqrt(($p * (1 - $p) + ($z * $z / (4 * $n))) / $n);
        $under = 1 + ($z * $z / $n);
        
        return ($left - $right) / $under;
    }

    /**
     * Automatically approve price if it meets certain criteria
     */
    public function shouldAutoApprove()
    {
        // Get threshold for this product
        $threshold = PriceThreshold::where('product_id', $this->product_id)->first();
        
        if (!$threshold) return false;

        // Check if price is within threshold
        if ($this->submitted_price < $threshold->min_price || 
            $this->submitted_price > $threshold->max_price) {
            return false;
        }

        // Check user reputation
        $userReputation = $this->user->reputation_score;
        if ($userReputation < 50) return false;

        // Check recent contributions
        $recentContributions = self::where('product_id', $this->product_id)
            ->where('market_id', $this->market_id)
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', 'approved')
            ->get();

        if ($recentContributions->isEmpty()) return true;

        // Calculate average of recent approved prices
        $avgPrice = $recentContributions->avg('submitted_price');
        $percentDiff = abs(($this->submitted_price - $avgPrice) / $avgPrice * 100);

        // If within 10% of recent average, auto-approve
        return $percentDiff <= 10;
    }
}
