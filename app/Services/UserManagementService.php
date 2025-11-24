<?php

namespace App\Services;

use App\Models\User;
use App\Enums\UserType;
use Illuminate\Support\Str;

class UserManagementService
{
    public function __construct(private User $user)
    {
    }

    /**
     * Get paginated list of users.
     *
     * @param string $user_type
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUsers($user_type = 'user', $filters = [])
    {
        $query = $this->user->where('user_type', $user_type);
        
        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'active') {
                $query->where('is_active', 0);
            } elseif ($filters['status'] === 'pending') {
                $query->where('is_active', 1);
            } elseif ($filters['status'] === 'blocked') {
                $query->where('is_active', 2);
            }
        }
        
        // Email verification filter
        if (isset($filters['is_verified']) && $filters['is_verified'] !== '') {
            if ($filters['is_verified'] === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($filters['is_verified'] === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }
        
        // Apply sorting
        $sort = $filters['sort'] ?? 'latest';
        $query = match ($sort) {
            'name_asc' => $query->orderBy('first_name', 'asc'),
            'name_desc' => $query->orderBy('first_name', 'desc'),
            'joined_asc' => $query->orderBy('created_at', 'asc'),
            'joined_desc' => $query->orderBy('created_at', 'desc'),
            default => $query->latest(),
        };
        
        return $query->paginate(pagination_limit());
    }

    /**
     * Get all users for export (without pagination)
     *
     * @param string $user_type
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsersForExport($user_type = 'user', $filters = [])
    {
        $query = $this->user->where('user_type', $user_type);
        
        // Apply same filters as getUsers
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'active') {
                $query->where('is_active', 0);
            } elseif ($filters['status'] === 'pending') {
                $query->where('is_active', 1);
            } elseif ($filters['status'] === 'blocked') {
                $query->where('is_active', 2);
            }
        }
        
        if (isset($filters['is_verified']) && $filters['is_verified'] !== '') {
            if ($filters['is_verified'] === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($filters['is_verified'] === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }
        
        return $query->latest()->get();
    }

    /**
     * Summary of getUserStats
     * @return array
     */
    /**
     * Get user statistics grouped by user_type and status.
     *
     * @return array
     */
    public function getUserStats(): array
    {
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
            ];
        }

        $result = [];

        foreach (['user', 'volunteer'] as $type) {
            $typeUsers = $users->where('user_type', $type);

            $result["total_{$type}s"] = $typeUsers->count();
            $result["active_{$type}s"] = $typeUsers->where('is_active', $statuses['active'])->count();
            $result["new_{$type}s_monthly"] = $typeUsers->where('created_at', '>=', $lastMonth)->count();
            $result["pending_{$type}s"] = $typeUsers->where('is_active', $statuses['pending'])->count();
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
            $imageName = handle_file_upload('users/', $data['image']->getClientOriginalExtension(), $data['image'], null);
            $data['image_path'] = $imageName;
        }
        unset($data['image']);

        $user = $this->user->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'user_type' => $data['user_type'] ?? 'user',
            'image_path' => $data['image_path'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'division' => $data['division'] ?? null,
            'dob' => $data['dob'] ?? null,
            'is_active' => !empty($data['is_active']),
            'subscribed_to_newsletter' => !empty($data['subscribed_to_newsletter']),
            'email_verified_at' => !empty($data['email_verified']) ? now() : null,
            'status' => $data['status'] ?? 'pending',
            'remember_token' => $data['remember_token'] ?? null,
            'referral_code' => $this->generateReferralCode(),
            'referred_by' => $data['referred_by'] ?? null,
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
            $imageName = handle_file_upload('users/', $data['image']->getClientOriginalExtension(), $data['image'], $oldImagePath);
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
            'username' => $data['username'] ?? $user->username,
            'password' => $data['password'],
            'user_type' => $data['user_type'] ?? $user->user_type,
            'image_path' => $data['image_path'] ?? $user->image_path,
            'phone' => $data['phone'] ?? $user->phone,
            'gender' => $data['gender'] ?? $user->gender,
            'address' => $data['address'] ?? $user->address,
            'city' => $data['city'] ?? $user->city,
            'division' => $data['division'] ?? $user->division,
            'dob' => $data['dob'] ?? $user->dob,
            'is_active' => !empty($data['is_active']),
            'subscribed_to_newsletter' => !empty($data['subscribed_to_newsletter']),
            'email_verified_at' => !empty($data['email_verified']) ? now() : null,
            'status' => $data['status'] ?? $user->status,
            'remember_token' => $data['remember_token'] ?? $user->remember_token,
        ]);

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
     * Get user type options for forms (API users only)
     *
     * @return array
     */
    public function getUserTypeOptions(): array
    {
        return [
            UserType::USER->value => 'User',
            UserType::VOLUNTEER->value => 'Volunteer',
        ];
    }
}
