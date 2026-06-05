<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsPulseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'recent_prices' => (int) $this['recent_prices'],
            'markets_total' => (int) $this['markets_total'],
            'items_total' => (int) $this['items_total'],
            'contributors' => (int) $this['contributors'],
            'window' => $this['window'],
        ];
    }
}
