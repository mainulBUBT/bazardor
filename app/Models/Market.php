<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasUuid;

class Market extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

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
        'zone_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'division' => 'string',
        'district' => 'string',
        'upazila_or_thana' => 'string',
        'zone_id' => 'string',
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
     * Get the opening hours associated with the market.
     */
    public function openingHours()
    {
        return $this->hasMany(MarketOperatingHour::class);
    }

    /**
     * Get the market prices for the market.
     */
    public function marketPrices()
    {
        return $this->hasMany(ProductMarketPrice::class, 'market_id');
    }

    public function creatorRecord()
    {
        return $this->morphOne(\App\Models\EntityCreator::class, 'creatable');
    }

    /**
     * Get the zone that owns the market.
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
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
        
        return $query->selectRaw("*, {$haversine} AS distance_km")
                    ->having('distance_km', '<=', $radius)
                    ->orderBy('distance_km');
    }

    /**
     * Scope to add distance calculation (without radius filter).
     */
    public function scopeWithDistance($query, $latitude, $longitude)
    {
        if ($latitude === 0 || $longitude === 0) {
            return $query->select('*');
        }

        $haversine = "(6371 * acos(cos(radians($latitude)) 
                     * cos(radians(latitude)) 
                     * cos(radians(longitude) - radians($longitude)) 
                     + sin(radians($latitude)) 
                     * sin(radians(latitude))))";
        
        return $query->selectRaw("*, {$haversine} AS distance_km")
                    ->orderBy('distance_km');
    }

    /**
     * Scope to filter markets that are currently open.
     */
    public function scopeOpen($query)
    {
        $today = ucfirst(strtolower(now()->format('l')));
        $currentTime = now()->format('H:i:s');

        return $query->with(['openingHours' => function ($q) use ($today) {
            $q->where('day', $today);
        }])
        ->whereHas('openingHours', function ($q) use ($today, $currentTime) {
            $q->where('day', $today)
              ->where('is_closed', 0)
              ->whereRaw("TIME(?) BETWEEN opening AND closing", [$currentTime]);
        });
    }

    /**
     * Scope to filter markets that are currently closed.
     */
    public function scopeClosed($query)
    {
        $today = ucfirst(strtolower(now()->format('l')));
        $currentTime = now()->format('H:i:s');

        return $query->with(['openingHours' => function ($q) use ($today) {
            $q->where('day', $today);
        }])
        ->whereDoesntHave('openingHours', function ($q) use ($today, $currentTime) {
            $q->where('day', $today)
              ->where('is_closed', 0)
              ->whereRaw("TIME(?) BETWEEN opening AND closing", [$currentTime]);
        });
    }

    /**
     * Get today's opening hours.
     */
    public function getTodayOpeningHoursAttribute()
    {
        $today = ucfirst(strtolower(now()->format('l')));
        $hours = $this->openingHours()->where('day', $today)->first();
        
        if (!$hours) {
            return null;
        }

        $currentTime = now()->format('H:i:s');
        $isOpen = !$hours->is_closed && $currentTime >= $hours->opening && $currentTime <= $hours->closing;

        return [
            'day' => $hours->day,
            'opening' => $hours->opening,
            'closing' => $hours->closing,
            'is_closed' => (bool) $hours->is_closed,
            'is_open' => $isOpen,
        ];
    }

    /**
     * Get distance in km (requires distance_km to be selected in query).
     */
    public function getDistanceKmAttribute()
    {
        return isset($this->attributes['distance_km']) 
            ? round($this->attributes['distance_km'], 2) 
            : null;
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

    /**
     * Calculate distance from given coordinates to this market
     * Uses Haversine formula for accurate distance calculation
     * 
     * @param float $latitude User's latitude
     * @param float $longitude User's longitude
     * @return float|null Distance in kilometers, null if market coordinates not set
     */
    public function getDistanceFrom(float $latitude, float $longitude): ?float
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($this->latitude - $latitude);
        $dLng = deg2rad($this->longitude - $longitude);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($latitude)) * cos(deg2rad($this->latitude)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Get count of active products in this market
     * 
     * @return int
     */
    public function getActiveProductsCount(): int
    {
        return $this->marketPrices()
            ->whereHas('product', function ($query) {
                $query->where('status', 'active')->where('is_visible', true);
            })
            ->distinct('product_id')
            ->count('product_id');
    }

    /**
     * Get count of open days for this market
     * 
     * @return int
     */
    public function getOpenDaysCount(): int
    {
        return $this->openingHours()->where('is_closed', false)->count();
    }
}
