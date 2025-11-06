<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketResource extends JsonResource
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
            'type' => $this->type,
            'description' => $this->description,
            'image_path' => $this->image_path,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'visibility' => (bool) $this->visibility,
            'division' => $this->division,
            'district' => $this->district,
            'upazila_or_thana' => $this->upazila_or_thana,
            'zone_id' => $this->zone_id,
            'distance_km' => $this->distance_km !== null ? round($this->distance_km, 2) : null,
            'is_open' => $this->getIsOpenStatus(),
            'opening_hours' => $this->whenLoaded('openingHours', function () {
                return $this->openingHours->map(fn($hour) => [
                    'day' => $hour->day,
                    'opening' => $hour->opening,
                    'closing' => $hour->closing,
                    'is_closed' => (bool) $hour->is_closed,
                ])->first();
            }),
            'market_information' => $this->whenLoaded('marketInformation', function () {
                return [
                    'is_non_veg' => (bool) $this->marketInformation?->is_non_veg,
                    'is_halal' => (bool) $this->marketInformation?->is_halal,
                    'is_parking' => (bool) $this->marketInformation?->is_parking,
                    'is_restroom' => (bool) $this->marketInformation?->is_restroom,
                    'is_home_delivery' => (bool) $this->marketInformation?->is_home_delivery,
                ];
            }),
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Determine if market is currently open
     */
    private function getIsOpenStatus(): bool
    {
        $openingHours = $this->openingHours;
        
        if ($openingHours->isEmpty()) {
            return false;
        }

        $hour = $openingHours->first();
        
        if ($hour->is_closed) {
            return false;
        }

        $now = now()->format('H:i:s');
        return $now >= $hour->opening && $now <= $hour->closing;
    }
}
