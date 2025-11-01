<?php

namespace App\Services;

use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminManagementService
{
    public function __construct(private Admin $admin)
    {
    }

    /**
     * Get paginated list of admins.
     *
     * @param string|null $search
     * @param array $with
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAdmins($search = null, $with = [])
    {
        return $this->admin->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when(!empty($with), function ($query) use ($with) {
                $query->with($with);
            })
            ->latest()
            ->paginate(pagination_limit());
    }

    /**
     * Store a new admin.
     *
     * @param array $data
     * @return Admin
     */
    public function store(array $data): Admin
    {
        $data['password'] = bcrypt($data['password']);

        $admin = $this->admin->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => !empty($data['is_active']),
        ]);

        // Assign role if provided
        if (!empty($data['role'])) {
            $role = Role::findByName($data['role'], 'admin');
            if ($role) {
                $admin->assignRole($role);
            }
        }

        return $admin;
    }

    /**
     * Update an admin.
     *
     * @param string $id
     * @param array $data
     * @return Admin
     */
    public function update($id, array $data): Admin
    {
        $admin = $this->findById($id);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update([
            'name' => $data['name'] ?? $admin->name,
            'email' => $data['email'] ?? $admin->email,
            'is_active' => !empty($data['is_active']),
        ] + (isset($data['password']) ? ['password' => $data['password']] : []));

        // Sync role if provided
        if (isset($data['role'])) {
            $admin->syncRoles([]);
            if (!empty($data['role'])) {
                $role = Role::findByName($data['role'], 'admin');
                if ($role) {
                    $admin->assignRole($role);
                }
            }
        }

        return $admin;
    }

    /**
     * Delete an admin.
     *
     * @param string $id
     * @return bool|null
     */
    public function delete($id): ?bool
    {
        $admin = $this->findById($id);
        return $admin->delete();
    }

    /**
     * Find admin by ID.
     *
     * @param string $id
     * @param array $with
     * @return Admin
     */
    public function findById($id, array $with = [])
    {
        return $this->admin->when(!empty($with), function ($query) use ($with) {
            $query->with($with);
        })->findOrFail($id);
    }

    /**
     * Get all roles for the admin guard.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles()
    {
        return Role::where('guard_name', 'admin')->get();
    }

    /**
     * Get all permissions for the admin guard.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPermissions()
    {
        return Permission::where('guard_name', 'admin')->get();
    }

    /**
     * Get role options for forms.
     *
     * @return array
     */
    public function getRoleOptions(): array
    {
        return $this->getAllRoles()->pluck('name', 'id')->toArray();
    }
}
