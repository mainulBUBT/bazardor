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
            // Assign user_type role (basic category)
            if (!$user->hasRole($user->user_type) && $user->user_type) {
                try {
                    $user->assignRole($user->user_type);
                } catch (\Exception $e) {
                    \Log::warning("User type role {$user->user_type} not found for user {$user->id}");
                }
            }

            // Assign functional role if specified
            if ($user->role_id) {
                try {
                    $functionalRole = \Spatie\Permission\Models\Role::find($user->role_id);
                    if ($functionalRole) {
                        $user->assignRole($functionalRole);
                    }
                } catch (\Exception $e) {
                    \Log::warning("Functional role {$user->role_id} not found for user {$user->id}");
                }
            }
        });

        static::updated(function (User $user) {
            // Handle user_type changes
            if ($user->isDirty('user_type') && $user->user_type) {
                // Remove old user_type roles
                $userTypeRoles = ['super_admin', 'moderator', 'volunteer', 'user'];
                foreach ($userTypeRoles as $roleType) {
                    if ($user->hasRole($roleType) && $roleType !== $user->user_type) {
                        $user->removeRole($roleType);
                    }
                }
                
                // Assign new user_type role
                try {
                    if (!$user->hasRole($user->user_type)) {
                        $user->assignRole($user->user_type);
                    }
                } catch (\Exception $e) {
                    \Log::warning("User type role {$user->user_type} not found for user {$user->id}");
                }
            }

            // Handle functional role changes
            if ($user->isDirty('role_id')) {
                // Remove old functional roles (keep user_type roles)
                $userTypeRoles = ['super_admin', 'moderator', 'volunteer', 'user'];
                $currentRoles = $user->roles()->whereNotIn('name', $userTypeRoles)->get();
                
                foreach ($currentRoles as $role) {
                    $user->removeRole($role);
                }

                // Assign new functional role
                if ($user->role_id) {
                    try {
                        $functionalRole = \Spatie\Permission\Models\Role::find($user->role_id);
                        if ($functionalRole) {
                            $user->assignRole($functionalRole);
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Functional role {$user->role_id} not found for user {$user->id}");
                    }
                }
            }
        });
    }

    public function createdEntities()
    {
        return $this->hasMany(\App\Models\EntityCreator::class);
    }

    /**
     * Get the functional role (role_id relationship)
     */
    public function functionalRole()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    /**
     * Get all effective permissions (from both user_type and functional role)
     */
    public function getAllEffectivePermissions()
    {
        $permissions = collect();
        
        // Get permissions from all assigned roles
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        
        return $permissions->unique('id');
    }

    /**
     * Check if user has a functional role assigned
     */
    public function hasFunctionalRole(): bool
    {
        return !is_null($this->role_id) && $this->functionalRole()->exists();
    }

    /**
     * Get the display name for the user's functional role
     */
    public function getFunctionalRoleName(): ?string
    {
        return $this->functionalRole?->name;
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
        // First check using Spatie's permission system
        if ($this->hasPermissionTo($permission->value)) {
            return true;
        }
        
        // Fallback to config-based permissions for backward compatibility
        $rolePermissions = config('roles')[$this->role] ?? [];
        if (in_array($permission->value, $rolePermissions)) {
            return true;
        }
        
        // Check if user has role-based permissions
        foreach ($this->roles as $role) {
            if ($role->hasPermissionTo($permission->value)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                if ($this->hasPermissionTo($permission)) {
                    return true;
                }
            } elseif ($permission instanceof \App\Enums\Permission) {
                if ($this->hasPermission($permission)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                if (!$this->hasPermissionTo($permission)) {
                    return false;
                }
            } elseif ($permission instanceof \App\Enums\Permission) {
                if (!$this->hasPermission($permission)) {
                    return false;
                }
            }
        }
        return true;
    }
}
