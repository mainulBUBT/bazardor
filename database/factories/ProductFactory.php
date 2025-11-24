<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'category_id' => Category::factory(),
            'unit_id' => Unit::factory(),
            'base_price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph(),
            'sku' => strtoupper($this->faker->unique()->bothify('PROD-###-???')),
            'image_path' => 'categories/demo-veg.png',
            'status' => 'active',
            'is_visible' => true,
            'is_featured' => $this->faker->boolean(10),
            'country_of_origin' => 'Bangladesh',
            'brand' => 'Local',
        ];
    }
}
