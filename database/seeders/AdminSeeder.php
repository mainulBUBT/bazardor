<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles for admin guard
        $roles = [
            'super-admin',
            'admin',
            'moderator',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'admin']);
        }

        // Create Super Admin
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'superadmin@bazardor.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super-admin');

        // Create Admin
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@bazordor.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // Create Moderator
        $moderator = Admin::firstOrCreate(
            ['email' => 'moderator@bazordor.com'],
            [
                'name' => 'Moderator',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $moderator->assignRole('moderator');
    }
}
