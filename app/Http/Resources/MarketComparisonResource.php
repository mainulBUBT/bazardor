<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketComparisonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'type' => $this['type'],
            'address' => $this['address'],
            'distance_km' => $this['distance_km'],
            'active_products_count' => $this['active_products_count'],
            'open_days_count' => $this['open_days_count'],
            'features' => [
                'non_veg_available' => $this['features']['non_veg_available'],
                'halal_available' => $this['features']['halal_available'],
                'parking_available' => $this['features']['parking_available'],
                'restroom_available' => $this['features']['restroom_available'],
                'home_delivery' => $this['features']['home_delivery'],
            ],
            'opening_hours' => $this['opening_hours'],
        ];
    }
}
