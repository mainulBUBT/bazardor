<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Enums\Permission as PermissionEnum;
use App\Enums\Role as RoleEnum;

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
            Permission::create(['name' => $permissionEnum->value]);
        }

        // Create roles and assign permissions
        $roles = [
            RoleEnum::SUPER_ADMIN->value => config('roles')[RoleEnum::SUPER_ADMIN->value] ?? [],
            RoleEnum::MODERATOR->value => config('roles')[RoleEnum::MODERATOR->value] ?? [],
            RoleEnum::VOLUNTEER->value => config('roles')[RoleEnum::VOLUNTEER->value] ?? [],
            RoleEnum::USER->value => config('roles')[RoleEnum::USER->value] ?? [],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($permissions);
        }

        // Assign roles to existing users
        $users = User::all();
        foreach ($users as $user) {
            $user->assignRole($user->role);
        }
    }
} 