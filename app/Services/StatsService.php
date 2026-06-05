<?php

namespace App\Services;

use App\Models\PriceContribution;
use App\Models\ProductMarketPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StatsService
{
    public function __construct(
        private PriceContribution $priceContribution,
        private ProductMarketPrice $productMarketPrice
    ) {}

    /**
     * Get pulse statistics for a zone.
     */
    public function getPulse(string $zoneId, string $window = '24h'): array
    {
        $cacheKey = "stats:pulse:{$zoneId}:{$window}";

        return Cache::remember($cacheKey, 60, function () use ($zoneId, $window) {
            $since = $this->resolveWindow($window);

            return [
                'recent_prices' => $this->countRecentPrices($zoneId, $since),
                'markets_total' => $this->countDistinctMarkets($zoneId),
                'items_total' => $this->countDistinctProducts($zoneId),
                'contributors' => $this->countContributors($zoneId, $since),
                'window' => $window,
            ];
        });
    }

    /**
     * Resolve the time window string to a Carbon cutoff timestamp.
     */
    private function resolveWindow(string $window): Carbon
    {
        return match ($window) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            default => now()->subDay(),
        };
    }

    /**
     * Count approved price contributions within the time window for the zone.
     */
    private function countRecentPrices(string $zoneId, Carbon $since): int
    {
        return $this->priceContribution
            ->where('status', 'approved')
            ->where('created_at', '>=', $since)
            ->whereHas('market', fn ($q) => $q->where('zone_id', $zoneId))
            ->count();
    }

    /**
     * Count distinct markets with at least one approved price in the zone.
     */
    private function countDistinctMarkets(string $zoneId): int
    {
        return $this->productMarketPrice
            ->whereHas('market', fn ($q) => $q->where('zone_id', $zoneId))
            ->distinct('market_id')
            ->count('market_id');
    }

    /**
     * Count distinct products with at least one approved price in the zone.
     */
    private function countDistinctProducts(string $zoneId): int
    {
        return $this->productMarketPrice
            ->whereHas('market', fn ($q) => $q->where('zone_id', $zoneId))
            ->distinct('product_id')
            ->count('product_id');
    }

    /**
     * Count distinct authenticated users who submitted an approved price in the window.
     */
    private function countContributors(string $zoneId, Carbon $since): int
    {
        return $this->priceContribution
            ->where('status', 'approved')
            ->where('created_at', '>=', $since)
            ->whereHas('market', fn ($q) => $q->where('zone_id', $zoneId))
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
    }
}
