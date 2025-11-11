<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class PriceContribution extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'product_id',
        'market_id',
        'user_id',
        'submitted_price',
        'proof_image',
        'status',
    ];

    protected $casts = [
        'product_id' => 'string',
        'market_id' => 'string',
        'user_id' => 'string',
        'submitted_price' => 'decimal:2',
        'status' => 'string',
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

}
