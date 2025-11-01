<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class EntityCreator extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'entity_creators';

    protected $fillable = [
        'user_id',
        'creatable_id',
        'creatable_type',
    ];

    protected $casts = [
        'user_id' => 'string',
        'creatable_id' => 'string',
    ];

    /**
     * The user who created the entity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The entity (market, product, etc.) that was created.
     */
    public function creatable()
    {
        return $this->morphTo();
    }
} 