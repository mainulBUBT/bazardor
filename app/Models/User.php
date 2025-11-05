<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
        'user_type' => 'string',
        'dob' => 'date',
        'is_active' => 'boolean',
        'subscribed_to_newsletter' => 'boolean',
        'status' => 'string',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = static::generateUniqueReferralCode();
            }

            if (!empty($user->username)) {
                return;
            }

            $user->username = static::generateUniqueUsername(
                $user->first_name,
                $user->last_name
            );
        });
    }

    protected static function generateUniqueUsername(?string $firstName, ?string $lastName): string
    {
        $base = trim(implode(' ', array_filter([
            trim((string) ($firstName ?? '')),
            trim((string) ($lastName ?? '')),
        ])));

        $baseUsername = $base !== '' ? Str::slug($base, '.') : 'user';

        if ($baseUsername === '') {
            $baseUsername = 'user';
        }

        $username = $baseUsername;
        $suffix = 1;

        while (static::where('username', $username)->exists()) {
            $username = $baseUsername . '.' . $suffix;
            $suffix++;
        }

        return $username;
    }

    protected static function generateUniqueReferralCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }

    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function createdEntities()
    {
        return $this->hasMany(EntityCreator::class);
    }
}
