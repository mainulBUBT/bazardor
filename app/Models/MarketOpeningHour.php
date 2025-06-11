<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketOpeningHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }
}
