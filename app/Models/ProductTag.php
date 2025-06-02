<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'tag',
    ];

    /**
     * Get the product that owns the tag.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
