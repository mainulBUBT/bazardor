<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Market extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'location',
        'type',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'is_active',
        'visibility',
        'is_featured',
        'position',
        'division',
        'district',
        'upazila_or_thana',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'integer',
        'is_featured' => 'integer',
        'division' => 'string',
        'district' => 'string',
        'upazila_or_thana' => 'string',
        'rating' => 'float',
        'rating_count' => 'integer',
        'position' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Get the products associated with the market.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    /**
     * Get the market information associated with the market.
     */
    public function marketInformation()
    {
        return $this->hasOne(MarketInformation::class);
    }

    /**
     * Get the market's average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->rating_count > 0 ? $this->rating / $this->rating_count : 0;
    }

    /**
     * Scope a query to only include active markets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Get markets within a certain radius (in kilometers).
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 5)
    {
        $haversine = "(6371 * acos(cos(radians($latitude)) 
                     * cos(radians(latitude)) 
                     * cos(radians(longitude) - radians($longitude)) 
                     + sin(radians($latitude)) 
                     * sin(radians(latitude))))";
        
        return $query->selectRaw("*, {$haversine} AS distance")
                    ->having('distance', '<=', $radius)
                    ->orderBy('distance');
    }

    /**
     * Get the opening hours associated with the market.
     */
    public function openingHours()
    {
        return $this->hasMany(MarketOperatingHour::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($market) {
            if (empty($market->slug)) {
                $market->slug = Str::slug($market, '-').Str::random(5);
            }
        });
    }

    public function creatorRecord()
    {
        return $this->morphOne(\App\Models\EntityCreator::class, 'creatable');
    }
}
