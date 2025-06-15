<?php

namespace App\Services;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Str;
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
    public function store(array $data): User
    {
        $data['password'] = bcrypt($data['password']);

        if (!empty($data['image'])) {
            $data['image_path'] = handle_file_upload('users/', $data['image']);
            unset($data['image']);
        }

        $nameParts = explode(' ', $data['name'], 2);

        $user = $this->user->create([
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? null,
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'image_path' => $data['image_path'] ?? null,
            'phone' => $data['phone'],
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'division' => $data['division'] ?? null,
            'date_of_birth' => $data['dob'] ?? null,
            'is_active' => !empty($data['is_active']),
            'subscribed_to_newsletter' => !empty($data['subscribed_to_newsletter']),
            'email_verified_at' => !empty($data['email_verified']) ? now() : null,
        ]);

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

        if (!empty($data['name'])) {
            $nameParts = explode(' ', $data['name'], 2);
            $data['first_name'] = $nameParts[0];
            $data['last_name'] = $nameParts[1] ?? null;
            unset($data['name']);
        }

        if (isset($data['dob'])) {
            $data['date_of_birth'] = $data['dob'];
            unset($data['dob']);
        }

        $user->update($data);

        return $user;
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

    private function generateReferralCode()
    {
        $referral_code = Str::random(6);
        if ($this->user->where('referral_code', $referral_code)->exists()) {
            return $this->generateReferralCode();
        }
        return $referral_code;
    }   
} 