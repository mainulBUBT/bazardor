<?php

namespace App\Enums;

enum MarketType: string
{
    case RETAIL = 'Retail Market';
    case WHOLESALE = 'Wholesale Market';
    case FARMERS = 'Farmers Market';
    case SUPERMARKET = 'Supermarket';
    case LOCAL_SHOP = 'Local Shop';
    case OTHER = 'Other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'value')
        );
    }
}
