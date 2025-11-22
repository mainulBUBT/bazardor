<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketsComparisonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'market_1' => new MarketComparisonResource($this['market_1']),
            'market_2' => new MarketComparisonResource($this['market_2']),
        ];
    }
}
