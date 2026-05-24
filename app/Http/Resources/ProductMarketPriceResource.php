<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMarketPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $priceDate = $this->price_date ? \Carbon\Carbon::parse($this->price_date) : null;
        $lastUpdate = $priceDate ? $priceDate->diffForHumans() : null;

        return [
            'id' => $this->id,
            'price' => (float) $this->price,
            'is_outdated' => $priceDate ? $priceDate->lt(now()->subDays(30)) : true,
            'price_trend' => $this->price_trend,
            'previous_price' => $this->previous_price !== null ? (float) $this->previous_price : null,
            'change_amount' => $this->change_amount !== null ? (float) $this->change_amount : null,
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
