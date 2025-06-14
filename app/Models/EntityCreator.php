<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityCreator extends Model
{
    use HasFactory;

    protected $table = 'entity_creators';

    protected $fillable = [
        'user_id',
        'creatable_id',
        'creatable_type',
    ];

    /**
     * The user who created the entity.
     */
    public function user()
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