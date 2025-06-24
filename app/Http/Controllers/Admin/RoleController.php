<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\Permission as PermissionEnum;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();
        $permissionGroups = $this->groupPermissionsByResource($permissions);
        
        return view('admin.roles.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be edited.');
        }

        $permissions = Permission::all();
        $permissionGroups = $this->groupPermissionsByResource($permissions);
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be updated.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['super_admin', 'moderator', 'volunteer', 'user'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Default roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Group permissions by resource.
     *
     * @param \Illuminate\Database\Eloquent\Collection $permissions
     * @return array
     */
    private function groupPermissionsByResource($permissions)
    {
        $groups = [];
        
        foreach ($permissions as $permission) {
            $parts = explode('_', $permission->name);
            
            // Skip if the permission name doesn't follow the expected format
            if (count($parts) < 2) {
                continue;
            }
            
            // Extract resource and action from permission name
            // For example: "manage_products" -> resource: "products", action: "manage"
            // Or "view_reports" -> resource: "reports", action: "view"
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
            
            // Map common actions
            if ($action === 'manage') {
                $groups[$resourceTitle]['manage'] = $permission;
            } elseif ($action === 'create') {
                $groups[$resourceTitle]['create'] = $permission;
            } elseif ($action === 'edit' || $action === 'update') {
                $groups[$resourceTitle]['edit'] = $permission;
            } elseif ($action === 'view' || $action === 'read') {
                $groups[$resourceTitle]['view'] = $permission;
            } elseif ($action === 'delete') {
                $groups[$resourceTitle]['delete'] = $permission;
            } elseif ($action === 'approve') {
                $groups[$resourceTitle]['approve'] = $permission;
            } else {
                // For other actions that don't fit the common pattern
                $groups[$resourceTitle]['other'][] = $permission;
            }
        }
        
        return $groups;
    }
} 