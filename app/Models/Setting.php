<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $casts = [
        "value"=> "array",    
    ];

    public function scopeSettingsType($query, $type)
    {
        $query->where('settings_type', $type);
    }
}
