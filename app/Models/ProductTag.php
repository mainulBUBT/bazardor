<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductTag extends Model implements TranslatableContract
{
    use HasUuid, Translatable;

    public $translatedAttributes = [
        'tag',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'product_id',
        'tag',
    ];

    protected $casts = [
        'product_id' => 'string',
    ];

    /**
     * Get the product that owns the tag.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
