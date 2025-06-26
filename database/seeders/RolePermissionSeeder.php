<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Enums\Permission as PermissionEnum;
use App\Enums\UserType;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        foreach (PermissionEnum::cases() as $permissionEnum) {
            Permission::firstOrCreate(['name' => $permissionEnum->value]);
        }

        // Create roles and assign permissions
        $roles = [
            UserType::SUPER_ADMIN->value => config('roles')[UserType::SUPER_ADMIN->value] ?? [],
            UserType::MODERATOR->value => config('roles')[UserType::MODERATOR->value] ?? [],
            UserType::VOLUNTEER->value => config('roles')[UserType::VOLUNTEER->value] ?? [],
            UserType::USER->value => config('roles')[UserType::USER->value] ?? [],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }

        // Assign roles to existing users
        $users = User::all();
        foreach ($users as $user) {
            $user->assignRole($user->role);
        }
    }
} 