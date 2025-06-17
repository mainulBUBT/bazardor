<?php

namespace App\Enums;

enum Permission: string
{
    case MANAGE_ADMINS = 'manage_admins';
    case MANAGE_USERS = 'manage_users';
    case MANAGE_PRODUCTS = 'manage_products';
    case MANAGE_PRICES = 'manage_prices';
    case MANAGE_MARKETS = 'manage_markets';
    case APPROVE_PRICE_CONTRIBUTIONS = 'approve_price_contributions';
    case MANAGE_CATEGORIES = 'manage_categories';
    case MANAGE_BANNERS = 'manage_banners';
    case VIEW_REPORTS = 'view_reports';
}