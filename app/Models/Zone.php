<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use App\Traits\HasUuid;
use App\Traits\SyncsTranslatedAttributes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Zone extends Model implements TranslatableContract
{
    use HasFactory, HasSpatial, HasUuid, Translatable, SyncsTranslatedAttributes {
        SyncsTranslatedAttributes::setAttribute insteadof Translatable;
    }

    public $translatedAttributes = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'is_active',
        'coordinates',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'coordinates' => Polygon::class,
    ];

    /**
     * Get the markets that belong to the zone.
     */
    public function markets(): HasMany
    {
        return $this->hasMany(Market::class);
    }

    /**
     * Get active zones.
     */
    public static function active()
    {
        return self::where('is_active', true);
    }
} 