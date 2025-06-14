<?php

namespace App\Services;

use App\Models\User;
use App\Enums\Role;

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
    public function getUsers($role = Role::USER, $search = null, $with = [])
    {
        return $this->user->where('role', $role)
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
    public function getUserStats(): array
    {
        $users = $this->user->get();

        return [
            'total_users' => $users->count(),
            'active_volunteers' => $users->where('role', Role::VOLUNTEER)->where('status', 'active')->count(),
            'new_users_monthly' => $users->where('created_at', '>=', now()->subMonth())->count(),
            'pending_verifications' => $users->where('status', 'pending')->count(),
        ];
    }

    /**
     * Summary of storeUser
     * @param array $data
     * @return User
     */
    public function storeUser(array $data): User
    {
        $data['password'] = bcrypt($data['password']);

        if (isset($data['image'])) {
            $data['image_path'] = handle_file_upload('user', $data['image']);
        }

        return $this->user->create($data);
    }
    
} 