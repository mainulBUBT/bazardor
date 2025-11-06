<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'favoritable_type' => $this->favoritable_type,
            'favoritable_id' => $this->favoritable_id,
            'favoritable' => $this->when(
                $this->relationLoaded('favoritable'),
                fn () => $this->resolveFavoritableResource()
            ),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }

    protected function resolveFavoritableResource()
    {
        return match ($this->favoritable_type) {
            'App\Models\Product' => [
                'id' => $this->favoritable->id,
                'name' => $this->favoritable->name,
                'image_path' => $this->favoritable->image_path,
                'base_price' => $this->favoritable->base_price,
            ],
            'App\Models\Market' => [
                'id' => $this->favoritable->id,
                'name' => $this->favoritable->name,
                'image_path' => $this->favoritable->image_path,
                'location' => $this->favoritable->location,
            ],
            default => $this->favoritable,
        };
    }
}
