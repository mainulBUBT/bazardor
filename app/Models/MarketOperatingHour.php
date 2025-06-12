<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketOperatingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_id',
        'day',
        'opening',
        'closing',
        'is_closed',
    ];  

}
