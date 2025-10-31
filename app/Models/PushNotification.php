<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',
        'target_audience',
        'zone_id',
        'link_url',
        'image',
        'sent_at',
        'status',
        'recipients_count',
        'opened_count',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'sent' => '<span class="badge badge-success">Sent</span>',
            'failed' => '<span class="badge badge-danger">Failed</span>',
            'partial' => '<span class="badge badge-warning">Partial Delivery</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }

    public function getOpenRateAttribute()
    {
        if ($this->recipients_count == 0) return 0;
        return round(($this->opened_count / $this->recipients_count) * 100, 1);
    }
}
