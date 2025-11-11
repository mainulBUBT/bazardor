<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceContributionHistory extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'price_contributions_history';

    protected $fillable = [
        'id',
        'product_id',
        'market_id',
        'user_id',
        'submitted_price',
        'proof_image',
        'status',
        'validated_at',
    ];

    protected $casts = [
        'product_id' => 'string',
        'market_id' => 'string',
        'user_id' => 'string',
        'submitted_price' => 'decimal:2',
        'validated_at' => 'datetime',
    ];
}
