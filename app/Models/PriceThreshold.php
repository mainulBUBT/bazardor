<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class PriceThreshold extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'product_id',
        'min_price',
        'max_price',
    ];

    protected $casts = [
        'product_id' => 'string',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the threshold.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
