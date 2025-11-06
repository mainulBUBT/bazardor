<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMarketPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $priceDate = $this->price_date ? \Carbon\Carbon::parse($this->price_date) : null;
        $lastUpdate = $priceDate ? $priceDate->diffForHumans() : null;

        return [
            'id' => $this->id,
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price !== null ? (float) $this->discount_price : null,
            'price_date' => $priceDate?->toISOString(),
            'last_update' => $lastUpdate,
            'market' => $this->whenLoaded('market', function () {
                return [
                    'id' => $this->market->id,
                    'name' => $this->market->name,
                ];
            }),
        ];
    }
}
