<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductComparisonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this['product_id'],
            'product_name' => $this['product_name'],
            'category' => $this['category'],
            'unit' => $this['unit'],
            'market_1' => [
                'price' => $this['market_1']['price'],
                'price_date' => $this['market_1']['price_date'],
                'available' => $this['market_1']['available'],
            ],
            'market_2' => [
                'price' => $this['market_2']['price'],
                'price_date' => $this['market_2']['price_date'],
                'available' => $this['market_2']['available'],
            ],
            'price_difference' => $this['price_difference'] ? [
                'amount' => $this['price_difference']['amount'],
                'percentage' => $this['price_difference']['percentage'],
                'cheaper_market' => $this['price_difference']['cheaper_market'],
            ] : null,
        ];
    }
}
