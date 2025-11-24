<?php

namespace Database\Factories;

use App\Models\Market;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductMarketPrice>
 */
class ProductMarketPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 1000);
        return [
            'product_id' => Product::factory(),
            'market_id' => Market::factory(),
            'price' => $price,
            'discount_price' => $this->faker->boolean(20) ? $price * 0.9 : null,
            'price_date' => now(),
        ];
    }
}
