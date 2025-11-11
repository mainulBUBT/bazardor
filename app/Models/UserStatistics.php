<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatistics extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'price_updates_count',
        'reviews_count',
        'products_added_count',
        'accurate_contributions_count',
        'inaccurate_contributions_count',
        'reputation_score',
        'tier',
        'last_price_update_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_updates_count' => 'int',
        'reviews_count' => 'int',
        'products_added_count' => 'int',
        'accurate_contributions_count' => 'int',
        'inaccurate_contributions_count' => 'int',
        'reputation_score' => 'float',
        'last_price_update_at' => 'datetime',
    ];

    /**
     * Get the user that owns the statistics record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
