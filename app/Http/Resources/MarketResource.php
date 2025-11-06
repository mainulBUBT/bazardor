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
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
