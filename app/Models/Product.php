<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasUuid;
use App\Models\PriceThreshold;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'category_id',
        'unit_id',
        'description',
        'status',
        'is_visible',
        'is_featured',
        'image_path',
        'sku',
        'barcode',
        'brand',
        'base_price',
        'country_of_origin',
        'added_by',
        'added_by_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category_id' => 'string',
        'unit_id' => 'string',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'base_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the unit that owns the product.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the tags for the product.
     */
    public function tags()
    {
        return $this->hasMany(ProductTag::class);
    }

    /**
     * Get the market prices for the product.
     */
    public function marketPrices(): HasMany
    {
        return $this->hasMany(ProductMarketPrice::class);
    }

    public function priceThreshold(): HasOne
    {
        return $this->hasOne(PriceThreshold::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include visible products.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the creator (User or Admin) who added this product.
     * This is a polymorphic relationship based on added_by field.
     */
    public function creator(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'added_by', 'added_by_id');
    }

    /**
     * Get the user who added this product (if added_by is 'user').
     */
    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_id')->where('added_by', 'user');
    }

    /**
     * Get the admin who added this product (if added_by is 'admin').
     */
    public function addedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'added_by_id')->where('added_by', 'admin');
    }

    public function creatorRecord(): MorphOne
    {
        return $this->morphOne(\App\Models\EntityCreator::class, 'creatable');
    }

    protected static function booted(): void
    {
        static::created(function (Product $product) {
            $product->ensureDefaultPriceThreshold();
        });

        static::updated(function (Product $product) {
            if ($product->isDirty('base_price')) {
                $product->ensureDefaultPriceThreshold();
            }
        });
    }

    public function ensureDefaultPriceThreshold(): void
    {
        if ($this->base_price === null) {
            return;
        }

        $basePrice = (float) $this->base_price;

        if ($basePrice <= 0) {
            return;
        }

        $tolerance = config('pricing.threshold_tolerance', 0.2); // 20% either side by default

        $minPrice = max(0.01, round($basePrice * (1 - $tolerance), 2));
        $maxPrice = round($basePrice * (1 + $tolerance), 2);

        PriceThreshold::query()->updateOrCreate(
            ['product_id' => $this->id],
            [
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ]
        );
    }
}