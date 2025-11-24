<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Zone>
 */
class ZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a simple triangle polygon for default
        $polygon = new Polygon([
            new LineString([
                new Point(0, 0),
                new Point(0, 1),
                new Point(1, 1),
                new Point(0, 0),
            ])
        ]);

        return [
            'name' => $this->faker->city() . ' Zone',
            'is_active' => true,
            'coordinates' => $polygon,
        ];
    }
}
