<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserType;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        static::created(function (User $user) {
            // Automatically assign a role based on user_type if not already assigned
            if (!$user->roles()->count() && $user->user_type) {
                $user->assignRole($user->user_type);
            }
        });

        static::updated(function (User $user) {
            // If user_type changed, sync the corresponding role
            if ($user->isDirty('user_type') && $user->user_type) {
                $user->syncRoles([$user->user_type]);
            }
        });
    }

    public function createdEntities()
    {
        return $this->hasMany(\App\Models\EntityCreator::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_type === UserType::SUPER_ADMIN->value || 
               $this->role === UserType::SUPER_ADMIN->value || 
               $this->hasRole(UserType::SUPER_ADMIN->value);
    }

    public function isModerator(): bool
    {
        return $this->user_type === UserType::MODERATOR->value || 
               $this->role === UserType::MODERATOR->value || 
               $this->hasRole(UserType::MODERATOR->value);
    }

    public function isVolunteer(): bool
    {
        return $this->user_type === UserType::VOLUNTEER->value || 
               $this->role === UserType::VOLUNTEER->value || 
               $this->hasRole(UserType::VOLUNTEER->value);
    }

    public function isUser(): bool
    {
        return $this->user_type === UserType::USER->value || 
               $this->role === UserType::USER->value || 
               $this->hasRole(UserType::USER->value);
    }

    // Legacy method for backward compatibility
    public function hasPermission(\App\Enums\Permission $permission): bool
    {   
        if ($this->hasPermissionTo($permission->value)) {
            return true;
        }
        
        $rolePermissions = config('roles')[$this->role] ?? [];
        return in_array($permission->value, $rolePermissions);
    }
}
