<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Enums\Permission as PermissionEnum;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    /**
     * Get all roles with their permissions
     *
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return Role::with('permissions')->get();
    }

    /**
     * Find a role by ID
     *
     * @param int|string $id
     * @return Role
     * @throws ModelNotFoundException
     */
    public function findById(int|string $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Store a new role with permissions
     *
     * @param array $data
     * @return Role
     */
    public function store(array $data): Role
    {
        DB::beginTransaction();
        
        try {
            $role = Role::create(['name' => $data['name']]);
            $role->syncPermissions($data['permissions']);
            
            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error($e->getMessage() ?: translate('messages.failed_to_create_role'));
            throw $e;
        }
    }

    /**
     * Update an existing role with permissions
     *
     * @param int|string $id
     * @param array $data
     * @return Role
     */
    public function update(int|string $id, array $data): Role
    {
        $role = $this->findById($id);
        
        // Check if this is a default role
        if ($role->name === 'super_admin') {
            Toastr::error(translate('messages.super_admin_role_cannot_be_updated'));
            return $role;
        }
        
        DB::beginTransaction();
        
        try {
            $role->update(['name' => $data['name']]);
            $role->syncPermissions($data['permissions']);
            
            DB::commit();
            return $role;
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error($e->getMessage() ?: translate('messages.failed_to_update_role'));
            throw $e;
        }
    }

    /**
     * Delete a role
     *
     * @param int|string $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $role = $this->findById($id);
        
        // Check if this is a default role
        if (in_array($role->name, ['super_admin', 'moderator', 'volunteer', 'user'])) {
            Toastr::error(translate('messages.default_roles_cannot_be_deleted'));
            return false;
        }
        
        try {
            return $role->delete();
        } catch (\Exception $e) {
            Toastr::error($e->getMessage() ?: translate('messages.failed_to_delete_role'));
            throw $e;
        }
    }

    /**
     * Get all permissions
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    /**
     * Group permissions by resource for better UI organization
     *
     * @param Collection $permissions
     * @return array
     */
    public function groupPermissionsByResource(Collection $permissions): array
    {
        $groups = [];
        
        foreach ($permissions as $permission) {
            $parts = explode('_', $permission->name);
            
            // Skip if the permission name doesn't follow the expected format
            if (count($parts) < 2) {
                continue;
            }
            
            // Extract resource and action from permission name
            $action = $parts[0];
            $resource = implode('_', array_slice($parts, 1));
            
            // Convert to title case for display
            $resourceTitle = ucwords(str_replace('_', ' ', $resource));
            
            if (!isset($groups[$resourceTitle])) {
                $groups[$resourceTitle] = [
                    'create' => null,
                    'edit' => null,
                    'view' => null,
                    'delete' => null,
                    'manage' => null,
                    'approve' => null,
                    'other' => []
                ];
            }
            
            // Map common actions to their respective categories
            match ($action) {
                'manage' => $groups[$resourceTitle]['manage'] = $permission,
                'create' => $groups[$resourceTitle]['create'] = $permission,
                'edit', 'update' => $groups[$resourceTitle]['edit'] = $permission,
                'view', 'read' => $groups[$resourceTitle]['view'] = $permission,
                'delete' => $groups[$resourceTitle]['delete'] = $permission,
                'approve' => $groups[$resourceTitle]['approve'] = $permission,
                default => $groups[$resourceTitle]['other'][] = $permission
            };
        }
        
        return $groups;
    }
    
    /**
     * Check if a role has specific permissions
     *
     * @param Role $role
     * @param array|string $permissions
     * @return bool
     */
    public function hasPermissions(Role $role, array|string $permissions): bool
    {
        if (is_string($permissions)) {
            return $role->hasPermissionTo($permissions);
        }
        
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission)) {
                return false;
            }
        }
        
        return true;
    }
} 