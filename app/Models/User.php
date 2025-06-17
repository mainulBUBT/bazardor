<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'dob' => 'date',
        'is_active' => 'boolean',
        'subscribed_to_newsletter' => 'boolean',
        'status' => 'string',
    ];

    public function createdEntities()
    {
        return $this->hasMany(\App\Models\EntityCreator::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Role::SUPER_ADMIN->value;
    }


    public function isModerator(): bool
    {
        return $this->role === Role::MODERATOR;
    }

    public function isVolunteer(): bool
    {
        return $this->role === Role::VOLUNTEER->value;
    }

    public function isUser(): bool
    {
        return $this->role === Role::USER->value;
    }

    public function hasPermission(\App\Enums\Permission $permission): bool
    {   
        $rolePermissions = config('roles')[$this->role] ?? [];
        return in_array($permission->value, $rolePermissions);
    }
}
