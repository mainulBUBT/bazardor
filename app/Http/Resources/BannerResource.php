<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image_path' => $this->image_path,
            'url' => $this->url,
            'type' => $this->type,
            'description' => $this->description,
            'badge_text' => $this->badge_text,
            'badge_color' => $this->badge_color,
            'badge_background_color' => $this->badge_background_color,
            'badge_icon' => $this->badge_icon,
            'button_text' => $this->button_text,
            'is_active' => (bool) $this->is_active,
            'position' => (int) $this->position,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'zone_id' => $this->zone_id ?? 0,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
        ];
    }
}
