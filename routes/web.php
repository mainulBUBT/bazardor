<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('admin.auth.login');
});

// Route to create initial users with roles (only for development)
Route::get('/create-initial-users', function () {
    // Only allow in development environment
    if (!app()->environment('local')) {
        return response()->json(['error' => 'This route is only available in development environment'], 403);
    }

    try {
        // Create roles if they don't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@bazardor.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'status' => 'active',
            ]
        );
        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Create Moderator
        $moderator = User::firstOrCreate(
            ['email' => 'moderator@bazardor.com'],
            [
                'first_name' => 'Moderator',
                'last_name' => 'User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'status' => 'active',
            ]
        );
        if (!$moderator->hasRole('moderator')) {
            $moderator->assignRole($moderatorRole);
        }

        // Create Regular User
        $user = User::firstOrCreate(
            ['email' => 'user@bazardor.com'],
            [
                'first_name' => 'Regular',
                'last_name' => 'User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'status' => 'active',
            ]
        );
        if (!$user->hasRole('user')) {
            $user->assignRole($userRole);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Initial users created successfully!',
            'users' => [
                [
                    'id' => $superAdmin->id,
                    'name' => $superAdmin->name,
                    'email' => $superAdmin->email,
                    'role' => 'super-admin',
                    'created' => $superAdmin->wasRecentlyCreated ? 'Yes' : 'Already existed'
                ],
                [
                    'id' => $moderator->id,
                    'name' => $moderator->name,
                    'email' => $moderator->email,
                    'role' => 'moderator',
                    'created' => $moderator->wasRecentlyCreated ? 'Yes' : 'Already existed'
                ],
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'user',
                    'created' => $user->wasRecentlyCreated ? 'Yes' : 'Already existed'
                ]
            ],
            'credentials' => [
                'default_password' => 'password123',
                'note' => 'Please change default passwords after first login'
            ]
        ]);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create users: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('test', function () {
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
                echo "{$action}_{$resource}";
            }
        }
});
