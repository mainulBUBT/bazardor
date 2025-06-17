<?php

use App\Enums\Permission;

return [
    'super_admin' => [
        Permission::MANAGE_ADMINS->value,
        Permission::MANAGE_USERS->value,
        Permission::MANAGE_PRODUCTS->value,
        Permission::MANAGE_PRICES->value,
        Permission::MANAGE_MARKETS->value,
        Permission::APPROVE_PRICE_CONTRIBUTIONS->value,
        Permission::MANAGE_CATEGORIES->value,
        Permission::MANAGE_BANNERS->value,
        Permission::VIEW_REPORTS->value,
    ],
    'moderator' => [
        Permission::MANAGE_USERS->value,
        Permission::MANAGE_PRODUCTS->value,
        Permission::MANAGE_PRICES->value,
        Permission::MANAGE_MARKETS->value,
        Permission::APPROVE_PRICE_CONTRIBUTIONS->value,
        Permission::MANAGE_CATEGORIES->value,
        Permission::MANAGE_BANNERS->value,
        Permission::VIEW_REPORTS->value,
    ],
    'volunteer' => [
        Permission::VIEW_REPORTS->value,
        Permission::APPROVE_PRICE_CONTRIBUTIONS->value,
    ],
    'user' => [
        // Basic or no permissions
    ],
]; 