<?php

namespace App\Enums;

class Location
{
    const BANGLADESH = [
        'Dhaka' => [
            'districts' => [
                'Dhaka' => ['Dhaka South', 'Dhaka North'],
                'Gazipur' => ['Gazipur Sadar', 'Tongi'],
            ],
        ],
        'Chittagong' => [
            'districts' => [
                'Chittagong' => ['Chittagong Sadar', 'Hathazari'],
                'Comilla' => ['Comilla Sadar', 'Laksam'],
            ],
        ],
        'Sylhet' => [
            'districts' => [
                'Sylhet' => ['Sylhet Sadar', 'Beanibazar'],
            ],
        ],
        'Rajshahi' => [
            'districts' => [
                'Rajshahi' => ['Rajshahi Sadar', 'Paba'],
            ],
        ],
        'Khulna' => [
            'districts' => [
                'Khulna' => ['Khulna Sadar', 'Sonadanga'],
            ],
        ],
        'Barisal' => [
            'districts' => [
                'Barisal' => ['Barisal Sadar', 'Bakerganj'],
            ],
        ],
        'Rangpur' => [
            'districts' => [
                'Rangpur' => ['Rangpur Sadar', 'Mithapukur'],
            ],
        ],
        'Mymensingh' => [
            'districts' => [
                'Mymensingh' => ['Mymensingh Sadar', 'Fulbaria'],
            ],
        ],
    ];

    public static function getDivisions(): array
    {
        return array_keys(self::BANGLADESH);
    }

    public static function getDistricts(string $division): array
    {
        return array_keys(self::BANGLADESH[$division]['districts'] ?? []);
    }

    public static function getThanas(string $division, string $district): array
    {
        return self::BANGLADESH[$division]['districts'][$district] ?? [];
    }

    public static function getAllLocations(): array
    {
        return self::BANGLADESH;
    }
}
