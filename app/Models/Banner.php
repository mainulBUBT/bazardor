<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\SyncsTranslatedAttributes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model implements TranslatableContract
{
    use HasFactory, HasUuid, SoftDeletes, Translatable, SyncsTranslatedAttributes {
        SyncsTranslatedAttributes::setAttribute insteadof Translatable;
    }

    public $translatedAttributes = [
        'title',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image_path',
        'link',
        'is_active',
        'is_featured',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope a query to only include featured banners.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * The zones this banner belongs to.
     * Empty relationship = all zones.
     */
    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'banner_zone', 'banner_id', 'zone_id')
            ->withTimestamps();
    }

    /**
     * Get the full URL for the banner's image.
     */
    public function getImageFullUrlAttribute(): string
    {
        return get_image_url($this->image_path, 'banners');
    }
}
