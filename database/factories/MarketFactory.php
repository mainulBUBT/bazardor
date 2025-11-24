<?php

namespace Database\Factories;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Market>
 */
class MarketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company() . ' Bazar';
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(23.7, 23.9), // Dhaka approx
            'longitude' => $this->faker->longitude(90.3, 90.5), // Dhaka approx
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'upazila_or_thana' => $this->faker->citySuffix(),
            'zone_id' => Zone::factory(),
            'image_path' => 'demo-veg.png',
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
            'visibility' => true,
        ];
    }
}
