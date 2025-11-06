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
            'image_path' => $this->image_path,
            'description' => $this->description,
            'status' => $this->status,
            'is_visible' => (bool) $this->is_visible,
            'is_featured' => (bool) $this->is_featured,
            'brand' => $this->brand,
            'country_of_origin' => $this->country_of_origin,
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
