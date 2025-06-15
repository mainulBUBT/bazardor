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
        'role' => Role::class,
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'subscribed_to_newsletter' => 'boolean',
    ];

    public function createdEntities()
    {
        return $this->hasMany(\App\Models\EntityCreator::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Role::SUPER_ADMIN;
    }


    public function isVolunteer(): bool
    {
        return $this->role === Role::VOLUNTEER;
    }

    public function isUser(): bool
    {
        return $this->role === Role::USER;
    }

    public function hasPermission(\App\Enums\Permission $permission): bool
    {
        $rolePermissions = config('roles')[$this->role->value] ?? [];
        return in_array($permission->value, $rolePermissions);
    }
}
