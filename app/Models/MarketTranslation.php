<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'address',
    ];
}
