<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketInformation extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_non_veg' => 'integer',
        'is_halalal' => 'integer',
        'is_parking' => 'integer',
        'is_restroom' => 'integer',
        'is_home_delivery' => 'integer',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }

}
