<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleStoreUpdateRequest;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService)
    {
    }

    /**
     * Display a listing of roles
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $roles = $this->roleService->getRoles();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show form for creating a new role
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $permissions = $this->roleService->getAllPermissions();
        $permissionGroups = $this->roleService->groupPermissionsByResource($permissions);
        
        return view('admin.roles.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created role
     * 
     * @param RoleStoreUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleStoreUpdateRequest $request)
    {
        $this->roleService->store($request->validated());
        Toastr::success(translate('messages.role_created_successfully'));
        
        return redirect()->route('admin.roles.index');
    }

    /**
     * Show form for editing a role
     * 
     * @param Role $role
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Role $role)
    {
        if ($role->name === 'super_admin') {
            Toastr::error(translate('messages.super_admin_role_cannot_be_edited'));
            return redirect()->route('admin.roles.index');
        }

        $permissions = $this->roleService->getAllPermissions();
        $permissionGroups = $this->roleService->groupPermissionsByResource($permissions);
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    /**
     * Update a role
     * 
     * @param RoleStoreUpdateRequest $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RoleStoreUpdateRequest $request, Role $role)
    {
        $this->roleService->update($role->id, $request->validated());
        Toastr::success(translate('messages.role_updated_successfully'));
        
        return redirect()->route('admin.roles.index');
    }

    /**
     * Delete a role
     * 
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        $this->roleService->delete($role->id);
        Toastr::success(translate('messages.role_deleted_successfully'));
        
        return redirect()->route('admin.roles.index');
    }
} 