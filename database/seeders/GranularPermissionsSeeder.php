<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GranularPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define resources and their actions
        $resources = [
            'products' => ['create', 'edit', 'view', 'delete'],
            'categories' => ['create', 'edit', 'view', 'delete'],
            'markets' => ['create', 'edit', 'view', 'delete'],
            'banners' => ['create', 'edit', 'view', 'delete'],
            'users' => ['create', 'edit', 'view', 'delete'],
            'admins' => ['create', 'edit', 'view', 'delete'],
            'prices' => ['create', 'edit', 'view', 'delete', 'approve'],
            'reports' => ['view'],
            'price_contributions' => ['approve'],
            'roles' => ['create', 'edit', 'view', 'delete'],
        ];

        // Create granular permissions
        foreach ($resources as $resource => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$resource}"]);
            }
        }

        // Map old permissions to new granular permissions
        $permissionMapping = [
            'manage_products' => ['create_products', 'edit_products', 'view_products', 'delete_products'],
            'manage_categories' => ['create_categories', 'edit_categories', 'view_categories', 'delete_categories'],
            'manage_markets' => ['create_markets', 'edit_markets', 'view_markets', 'delete_markets'],
            'manage_banners' => ['create_banners', 'edit_banners', 'view_banners', 'delete_banners'],
            'manage_users' => ['create_users', 'edit_users', 'view_users', 'delete_users'],
            'manage_admins' => ['create_admins', 'edit_admins', 'view_admins', 'delete_admins'],
            'manage_prices' => ['create_prices', 'edit_prices', 'view_prices', 'delete_prices'],
            'view_reports' => ['view_reports'],
            'approve_price_contributions' => ['approve_price_contributions'],
            'manage_roles' => ['create_roles', 'edit_roles', 'view_roles', 'delete_roles'],
        ];

        // Update existing roles with granular permissions
        $roles = Role::all();
        foreach ($roles as $role) {
            $rolePermissions = $role->permissions->pluck('name')->toArray();
            $newPermissions = [];

            foreach ($rolePermissions as $oldPermission) {
                if (isset($permissionMapping[$oldPermission])) {
                    $newPermissions = array_merge($newPermissions, $permissionMapping[$oldPermission]);
                } else {
                    $newPermissions[] = $oldPermission;
                }
            }

            $role->syncPermissions($newPermissions);
        }
    }
} 