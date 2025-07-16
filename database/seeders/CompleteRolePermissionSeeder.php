<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Enums\UserType;

class CompleteRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Product permissions
            'create_products',
            'edit_products', 
            'view_products',
            'delete_products',

            // Category permissions
            'create_categories',
            'edit_categories',
            'view_categories', 
            'delete_categories',

            // Market permissions
            'create_markets',
            'edit_markets',
            'view_markets',
            'delete_markets',

            // Banner permissions
            'create_banners',
            'edit_banners',
            'view_banners',
            'delete_banners',

            // User permissions
            'create_users',
            'edit_users',
            'view_users',
            'delete_users',

            // Admin permissions
            'create_admins',
            'edit_admins',
            'view_admins',
            'delete_admins',

            // Price permissions
            'create_prices',
            'edit_prices',
            'view_prices',
            'delete_prices',
            'approve_prices',

            // Role permissions
            'create_roles',
            'edit_roles',
            'view_roles',
            'delete_roles',

            // Zone permissions
            'create_zones',
            'edit_zones',
            'view_zones',
            'delete_zones',
            'manage_zones',

            // Settings permissions
            'view_settings',
            'edit_settings',
            'manage_settings',

            // Report permissions
            'view_reports',
            'view_analytics',
            'export_data',

            // Price contribution permissions
            'approve_price_contributions',
            'reject_price_contributions',

            // Legacy permissions for backward compatibility
            'manage_products',
            'manage_categories',
            'manage_markets',
            'manage_banners',
            'manage_users',
            'manage_admins',
            'manage_prices',
            'manage_roles',
            'view_reports',
            'approve_price_contributions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define user_type role permissions (basic categories)
        $userTypeRolePermissions = [
            UserType::SUPER_ADMIN->value => $permissions, // Super admin gets all permissions
            
            UserType::MODERATOR->value => [
                'view_products', 'edit_products', 'create_products',
                'view_categories', 'edit_categories', 'create_categories',
                'view_markets', 'edit_markets', 'create_markets',
                'view_banners', 'edit_banners', 'create_banners',
                'view_users', 'edit_users',
                'view_prices', 'edit_prices', 'create_prices', 'approve_prices',
                'view_zones', 'edit_zones',
                'view_reports', 'view_analytics',
                'approve_price_contributions', 'reject_price_contributions',
                // Legacy permissions
                'manage_products', 'manage_categories', 'manage_markets',
                'manage_banners', 'manage_prices', 'view_reports',
                'approve_price_contributions',
            ],
            
            UserType::VOLUNTEER->value => [
                'view_products', 'create_products', 'edit_products',
                'view_categories',
                'view_markets', 'create_markets', 'edit_markets',
                'view_prices', 'create_prices', 'edit_prices',
                'view_zones',
                // Legacy permissions
                'manage_products', 'manage_markets', 'manage_prices',
            ],
            
            UserType::USER->value => [
                'view_products',
                'view_categories',
                'view_markets',
                'view_prices',
            ],
        ];

        // Create user_type roles and assign permissions
        foreach ($userTypeRolePermissions as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Define functional roles (role_id based roles)
        $functionalRoles = [
            'Zone Manager' => [
                'view_zones', 'edit_zones', 'create_zones',
                'view_markets', 'edit_markets', 'create_markets',
                'view_products', 'edit_products', 'create_products',
                'view_prices', 'edit_prices', 'create_prices',
                'view_users', 'edit_users',
                'view_reports',
                'manage_zones',
            ],
            'Content Manager' => [
                'view_products', 'edit_products', 'create_products', 'delete_products',
                'view_categories', 'edit_categories', 'create_categories', 'delete_categories',
                'view_banners', 'edit_banners', 'create_banners', 'delete_banners',
                'view_markets', 'edit_markets',
                'manage_products', 'manage_categories', 'manage_banners',
            ],
            'Price Manager' => [
                'view_prices', 'edit_prices', 'create_prices', 'approve_prices',
                'view_products', 'edit_products',
                'view_markets', 'edit_markets',
                'approve_price_contributions', 'reject_price_contributions',
                'view_reports',
                'manage_prices', 'approve_price_contributions',
            ],
            'User Manager' => [
                'view_users', 'edit_users', 'create_users',
                'view_reports',
                'manage_users',
            ],
            'Report Analyst' => [
                'view_reports', 'view_analytics', 'export_data',
                'view_products', 'view_markets', 'view_users', 'view_prices',
            ],
        ];

        // Create functional roles and assign permissions
        foreach ($functionalRoles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // Assign roles to existing users
        $users = User::all();
        foreach ($users as $user) {
            // Assign user_type role if not already assigned
            if ($user->user_type && !$user->hasRole($user->user_type)) {
                try {
                    $user->assignRole($user->user_type);
                } catch (\Exception $e) {
                    \Log::warning("Could not assign user_type role {$user->user_type} to user {$user->id}: " . $e->getMessage());
                }
            }

            // Assign functional role if role_id is set
            if ($user->role_id && !$user->hasFunctionalRole()) {
                try {
                    $functionalRole = Role::find($user->role_id);
                    if ($functionalRole) {
                        $user->assignRole($functionalRole);
                    }
                } catch (\Exception $e) {
                    \Log::warning("Could not assign functional role {$user->role_id} to user {$user->id}: " . $e->getMessage());
                }
            }
        }

        $this->command->info('User type roles and functional roles have been seeded successfully!');
        $this->command->info('User type roles: ' . implode(', ', array_keys($userTypeRolePermissions)));
        $this->command->info('Functional roles: ' . implode(', ', array_keys($functionalRoles)));
    }
}