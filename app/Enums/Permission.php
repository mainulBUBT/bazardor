<?php

namespace App\Enums;

enum Permission: string
{
    // Legacy permissions for backward compatibility
    case MANAGE_ADMINS = 'manage_admins';
    case MANAGE_USERS = 'manage_users';
    case MANAGE_PRODUCTS = 'manage_products';
    case MANAGE_PRICES = 'manage_prices';
    case MANAGE_MARKETS = 'manage_markets';
    case APPROVE_PRICE_CONTRIBUTIONS = 'approve_price_contributions';
    case MANAGE_CATEGORIES = 'manage_categories';
    case MANAGE_BANNERS = 'manage_banners';
    case VIEW_REPORTS = 'view_reports';

    // Granular permissions
    case CREATE_PRODUCTS = 'create_products';
    case EDIT_PRODUCTS = 'edit_products';
    case VIEW_PRODUCTS = 'view_products';
    case DELETE_PRODUCTS = 'delete_products';

    case CREATE_CATEGORIES = 'create_categories';
    case EDIT_CATEGORIES = 'edit_categories';
    case VIEW_CATEGORIES = 'view_categories';
    case DELETE_CATEGORIES = 'delete_categories';

    case CREATE_MARKETS = 'create_markets';
    case EDIT_MARKETS = 'edit_markets';
    case VIEW_MARKETS = 'view_markets';
    case DELETE_MARKETS = 'delete_markets';

    case CREATE_BANNERS = 'create_banners';
    case EDIT_BANNERS = 'edit_banners';
    case VIEW_BANNERS = 'view_banners';
    case DELETE_BANNERS = 'delete_banners';

    case CREATE_USERS = 'create_users';
    case EDIT_USERS = 'edit_users';
    case VIEW_USERS = 'view_users';
    case DELETE_USERS = 'delete_users';

    case CREATE_ADMINS = 'create_admins';
    case EDIT_ADMINS = 'edit_admins';
    case VIEW_ADMINS = 'view_admins';
    case DELETE_ADMINS = 'delete_admins';

    case CREATE_PRICES = 'create_prices';
    case EDIT_PRICES = 'edit_prices';
    case VIEW_PRICES = 'view_prices';
    case DELETE_PRICES = 'delete_prices';
    case APPROVE_PRICES = 'approve_prices';

    // Role management permissions
    case CREATE_ROLES = 'create_roles';
    case EDIT_ROLES = 'edit_roles';
    case VIEW_ROLES = 'view_roles';
    case DELETE_ROLES = 'delete_roles';
}