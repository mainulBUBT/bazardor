<?php

namespace App\Services;

use App\Models\User;
use App\Enums\UserType;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class UserManagementService
{
    public function __construct(private User $user)
    {
    }

    /**
     * Get paginated list of users.
     *
     * @param string|null $search
     * @param array $with
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsers($user_type = UserType::USER->value, $search = null, $with = [])
    {
        return $this->user->where('user_type', $user_type)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(!empty($with), function ($query) use ($with) {
                $query->with($with);
            })
            ->latest()
            ->paginate(pagination_limit());
    }

    /**
     * Summary of getUserStats
     * @return array
     */
    /**
     * Get user statistics grouped by role and status.
     *
     * @return array
     */
    public function getUserStats(): array
    {
        $roles = [
            'user' => UserType::USER,
            'volunteer' => UserType::VOLUNTEER,
            'moderator' => UserType::MODERATOR,
        ];

        $statuses = [
            'active' => 1,
            'pending' => 0,
        ];

        $now = now();
        $lastMonth = $now->copy()->subMonth();

        $users = $this->user->get();

        if ($users->isEmpty()) {
            return [
                'total_users' => 0,
                'active_users' => 0,
                'new_users_monthly' => 0,
                'pending_users' => 0,
                'total_volunteers' => 0,
                'active_volunteers' => 0,
                'new_volunteers_monthly' => 0,
                'pending_volunteers' => 0,
                'total_moderators' => 0,
                'active_moderators' => 0,
                'new_moderators_monthly' => 0,
                'pending_moderators' => 0,
            ];
        }

        $result = [];

        foreach ($roles as $label => $role) {
            $roleUsers = $users->where('role', $role);

            $result["total_{$label}s"] = $roleUsers->count();
            $result["active_{$label}s"] = $roleUsers->where('status', $statuses['active'])->count();
            $result["new_{$label}s_monthly"] = $roleUsers->where('created_at', '>=', $lastMonth)->count();
            $result["pending_{$label}s"] = $roleUsers->where('status', $statuses['pending'])->count();
        }

        return $result;
    }

    /**
     * Summary of storeUser
     * @param array $data
     * @return User
     */
    public function store(array $data): User
    {
        $data['password'] = bcrypt($data['password']);

        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = handle_file_upload('users/', $data['image']->getClientOriginalExtension(), $data['image'],  null);
            $data['image_path'] = $imageName;
        }
        unset($data['image']);

        $user = $this->user->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'user_type' => $data['role'],
            'image_path' => $data['image_path'] ?? null,
            'phone' => $data['phone'],
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'division' => $data['division'] ?? null,
            'dob' => $data['dob'] ?? null,
            'is_active' => !empty($data['is_active']),
            'subscribed_to_newsletter' => !empty($data['subscribed_to_newsletter']),
            'email_verified_at' => !empty($data['email_verified']) ? now() : null,
        ]);

        // Assign user type role
        $user->assignRole($data['role']);

        // Assign additional functional roles if specified
        if (isset($data['functional_roles']) && is_array($data['functional_roles'])) {
            foreach ($data['functional_roles'] as $roleId) {
                $user->assignRole($roleId);
            }
        }

        $user->update([
            'referral_code' => $this->generateReferralCode(),
            'referred_by' => $data['referred_by'],
        ]);

        return $user;
    }

    /**
     * Summary of update
     * @param string $id
     * @param array $data
     * @return User
     */
    public function update($id, array $data): User
    {
        $user = $this->findById($id);
        $oldImagePath = $user->image_path;
       
        if (!is_null($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            $data['password'] = $user->password;
        }

        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = handle_file_upload('users/', $data['image']->getClientOriginalExtension(), $data['image'],  $oldImagePath);
            $data['image_path'] = $imageName;

            if ($oldImagePath) {
                $oldFilename = basename($oldImagePath);
                handle_file_upload('users/', '', null, $oldFilename);
            }
        }
        unset($data['image']);

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'password' => $data['password'],
            'role' => $data['role'],
            'user_type' => $data['role'],
            'image_path' => $data['image_path'] ?? $user->image_path,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'division' => $data['division'] ?? null,
            'dob' => $data['dob'] ?? null,
            'is_active' => !empty($data['is_active']),
            'subscribed_to_newsletter' => !empty($data['subscribed_to_newsletter']),
            'email_verified_at' => !empty($data['email_verified']) ? now() : null,
        ]);

        // Ensure user has the base user type role
        $user->assignRole($data['role']);
        
        // Update functional roles
        $userTypeRoles = [
            UserType::SUPER_ADMIN->value,
            UserType::MODERATOR->value,
            UserType::VOLUNTEER->value,
            UserType::USER->value
        ];
        
        // Get current functional roles (excluding user type roles)
        $currentFunctionalRoles = $user->roles->whereNotIn('name', $userTypeRoles)->pluck('id')->toArray();
        
        // Sync functional roles if provided
        if (isset($data['functional_roles']) && is_array($data['functional_roles'])) {
            // Remove old functional roles
            foreach ($currentFunctionalRoles as $roleId) {
                $user->removeRole($roleId);
            }
            
            // Add new functional roles
            foreach ($data['functional_roles'] as $roleId) {
                $user->assignRole($roleId);
            }
        } else {
            // If no functional roles provided, remove all existing ones
            foreach ($currentFunctionalRoles as $roleId) {
                $user->removeRole($roleId);
            }
        }

        return $user;
    }

    /**
     * Delete a user by ID.
     *
     * @param string $id
     * @return bool|null
     */
    public function delete($id): ?bool
    {
        $user = $this->findById($id);
        return $user->delete();
    }

    /**
     * Summary of findById
     * @param string $id
     * @return User|\Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function findById($id, array $with = [])
    {
        $query = $this->user->when(!empty($with), function ($query) use ($with) {
            $query->with($with);
        })->findOrFail($id);

        return $query; 
    }

    /**
     * Generate a unique referral code.
     *
     * @return string
     */

    private function generateReferralCode()
    {
        $referral_code = Str::random(6);
        if ($this->user->where('referral_code', $referral_code)->exists()) {
            return $this->generateReferralCode();
        }
        return $referral_code;
    }   

    /**
     * Get paginated list of pending users.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPendingUsers()
    {
        return $this->user->where('status', 'pending')
            ->latest()
            ->paginate(pagination_limit());
    }

    /**
     * Approve a pending user.
     *
     * @param string $id
     * @return bool
     */
    public function approveUser(string $id): bool
    {
        $user = $this->user->findOrFail($id);
        return $user->update([
            'status' => 'active',
            'email_verified_at' => now()
        ]);
    }

    /**
     * Reject a pending user.
     *
     * @param string $id
     * @return bool
     */
    public function rejectUser(string $id): bool
    {
        $user = $this->user->findOrFail($id);
        return $user->update([
            'status' => 'rejected'
        ]);
    }

    /**
     * Get all available roles for assignment
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        return SpatieRole::all();
    }
}
