<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_path' => $this->image_full_url,
            'description' => $this->description,
            'status' => $this->status,
            'is_visible' => (bool) $this->is_visible,
            'is_featured' => (bool) $this->is_featured,
            'brand' => $this->brand,
            'country_of_origin' => $this->country_of_origin,
            'base_price' => $this->base_price !== null ? (float) $this->base_price : null,
            'price_range' => $this->whenLoaded('priceThreshold', function () {
                return $this->priceThreshold ? [
                    'min' => (float) $this->priceThreshold->min_price,
                    'max' => (float) $this->priceThreshold->max_price,
                ] : null;
            }),
            'category' => CategoryResource::make(
                $this->whenLoaded('category')
            ),
            'unit' => UnitResource::make(
                $this->whenLoaded('unit')
            ),
            'market_prices' => ProductMarketPriceResource::collection(
                $this->whenLoaded('marketPrices')
            ),
        ];
    }
}
