<?php

namespace App\Enums;

enum Permission: string
{
    case MANAGE_ADMINS = 'manage_admins';
    case MANAGE_USERS = 'manage_users';
    case MANAGE_PRODUCTS = 'manage_products';
    case VIEW_REPORTS = 'view_reports';
    // Add more as needed
} 