<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryMarketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_path' => $this->image_path,
            'position' => $this->position,
            'is_active' => (bool) $this->is_active,
            'unique_market_count' => (int) $this->unique_market_count,
            'product_count' => (int) $this->product_count,
        ];
    }
}
