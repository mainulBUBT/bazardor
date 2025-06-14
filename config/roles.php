<?php

use App\Enums\Permission;

return [
    'super_admin' => [
        Permission::MANAGE_ADMINS->value,
        Permission::MANAGE_USERS->value,
        Permission::MANAGE_PRODUCTS->value,
        Permission::VIEW_REPORTS->value,
        // Add all permissions here for super_admin
    ],
    'admin' => [
        Permission::MANAGE_USERS->value,
        Permission::MANAGE_PRODUCTS->value,
        Permission::VIEW_REPORTS->value,
    ],
    'volunteer' => [
        Permission::VIEW_REPORTS->value,
    ],
    'user' => [
        // Basic or no permissions
    ],
]; 