<?php

namespace App\Services;

use App\Models\PriceContribution;
use App\Models\PriceContributionHistory;
use App\Models\PriceThreshold;
use App\Models\ProductMarketPrice;
use App\Models\UserStatistics;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PriceContributionProcessor
{
    public function __construct(
        private PriceContribution $contribution,
        private ProductMarketPrice $productMarketPrice,
        private PriceContributionHistory $history,
        private UserStatistics $userStatistics
    ) {
    }

    /**
     * Process all pending price contributions grouped by product and market.
     */
    public function processPending(): array
    {
        $summary = [
            'groups_processed' => 0,
            'contributions_processed' => 0,
            'prices_updated' => 0,
        ];

        $groupKeys = $this->contribution
            ->newQuery()
            ->select('product_id', 'market_id')
            ->where('status', 'pending')
            ->distinct()
            ->get();

        foreach ($groupKeys as $key) {
            $result = $this->processGroup(
                productId: $key->product_id,
                marketId: $key->market_id
            );

            if ($result['contributions'] === 0) {
                continue;
            }

            $summary['groups_processed']++;
            $summary['contributions_processed'] += $result['contributions'];
            $summary['prices_updated'] += $result['price_updated'] ? 1 : 0;
        }

        return $summary;
    }

    private function processGroup(string $productId, string $marketId): array
    {
        return DB::transaction(function () use ($productId, $marketId) {
            /** @var EloquentCollection<int, PriceContribution> $contributions */
            $contributions = $this->contribution
                ->newQuery()
                ->where('product_id', $productId)
                ->where('market_id', $marketId)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            if ($contributions->isEmpty()) {
                return [
                    'contributions' => 0,
                    'price_updated' => false,
                ];
            }

            $threshold = PriceThreshold::query()
                ->where('product_id', $productId)
                ->first();

            // Zone-scoped prices for IQR validation (one extra query per group)
            $zoneId = DB::table('markets')->where('id', $marketId)->value('zone_id');
            $zonePrices = collect();
            if ($zoneId) {
                $zonePrices = DB::table('product_market_prices')
                    ->join('markets', 'markets.id', '=', 'product_market_prices.market_id')
                    ->where('product_market_prices.product_id', $productId)
                    ->where('markets.zone_id', $zoneId)
                    ->orderBy('product_market_prices.price')
                    ->pluck('product_market_prices.price')
                    ->map(fn($p) => (float) $p)
                    ->values();
            }

            [$valid, $invalid] = $this->partitionContributions($contributions, $threshold, $zonePrices);

            $timestamp = CarbonImmutable::now();

            $priceUpdated = false;

            if ($valid->isNotEmpty()) {
                $officialPrice = round($valid->avg('submitted_price'), 2);

                $this->productMarketPrice
                    ->newQuery()
                    ->updateOrCreate(
                        [
                            'product_id' => $productId,
                            'market_id' => $marketId,
                        ],
                        [
                            'price' => $officialPrice,
                            'price_date' => $timestamp,
                        ]
                    );

                $priceUpdated = true;
                $this->recalibrateThreshold($productId);
            }

            $historyPayload = [];

            foreach ($contributions as $contribution) {
                $isValid = $valid->contains(fn (PriceContribution $item) => $item->id === $contribution->id);

                if ($contribution->user_id) {
                    $this->updateUserStatistics(
                        userId: $contribution->user_id,
                        isValid: $isValid,
                        timestamp: $timestamp
                    );
                }

                $historyPayload[] = [
                    'id' => $contribution->id,
                    'product_id' => $contribution->product_id,
                    'market_id' => $contribution->market_id,
                    'user_id' => $contribution->user_id,
                    'device_id' => $contribution->device_id,
                    'submitted_price' => $contribution->submitted_price,
                    'proof_image' => $contribution->proof_image,
                    'status' => $isValid ? 'validated' : 'invalid',
                    'validated_at' => $timestamp,
                    'created_at' => $contribution->created_at,
                    'updated_at' => $timestamp,
                ];
            }

            if (!empty($historyPayload)) {
                $this->history->newQuery()->upsert($historyPayload, ['id']);
            }

            $this->contribution
                ->newQuery()
                ->whereIn('id', $contributions->pluck('id'))
                ->forceDelete();

            return [
                'contributions' => $contributions->count(),
                'price_updated' => $priceUpdated,
            ];
        });
    }

    private function partitionContributions(
        EloquentCollection $contributions,
        ?PriceThreshold $threshold,
        Collection $zonePrices
    ): array {
        $valid   = new Collection();
        $invalid = new Collection();

        foreach ($contributions as $contribution) {
            if ($this->isContributionRealistic($contribution, $threshold, $zonePrices)) {
                $valid->push($contribution);
            } else {
                $invalid->push($contribution);
            }
        }

        return [$valid, $invalid];
    }

    private function isContributionRealistic(
        PriceContribution $contribution,
        ?PriceThreshold $threshold,
        Collection $zonePrices
    ): bool {
        if ($contribution->submitted_price <= 0) {
            return false;
        }

        // Prefer zone-scoped IQR over static threshold
        $bounds = $this->computeIqrBounds($zonePrices);
        if ($bounds !== null) {
            return $contribution->submitted_price >= $bounds['lower']
                && $contribution->submitted_price <= $bounds['upper'];
        }

        // Fallback: global product threshold
        if ($threshold === null) {
            return true;
        }

        return $contribution->submitted_price >= $threshold->min_price
            && $contribution->submitted_price <= $threshold->max_price;
    }

    private function computeIqrBounds(Collection $sortedPrices): ?array
    {
        $count = $sortedPrices->count();
        if ($count < config('pricing.min_samples_for_calibration', 3)) {
            return null;
        }

        $q1  = $sortedPrices[(int) floor(($count - 1) * 0.25)];
        $q3  = $sortedPrices[(int) floor(($count - 1) * 0.75)];
        $iqr = $q3 - $q1;

        return [
            'lower' => max(0.01, $q1 - 1.5 * $iqr),
            'upper' => $q3 + 1.5 * $iqr,
        ];
    }

    private function recalibrateThreshold(string $productId): void
    {
        $prices = $this->productMarketPrice
            ->newQuery()
            ->where('product_id', $productId)
            ->pluck('price')
            ->map(fn($p) => (float) $p)
            ->sort()
            ->values();

        if ($prices->count() < config('pricing.min_samples_for_calibration', 3)) {
            return;
        }

        $median    = compute_median($prices);
        $tolerance = config('pricing.threshold_tolerance', 0.20);

        PriceThreshold::query()->updateOrCreate(
            ['product_id' => $productId],
            [
                'min_price' => max(0.01, round($median * (1 - $tolerance), 2)),
                'max_price' => round($median * (1 + $tolerance), 2),
            ]
        );
    }

    private function updateUserStatistics(string $userId, bool $isValid, CarbonImmutable $timestamp): void
    {
        $stats = $this->userStatistics
            ->newQuery()
            ->firstOrCreate(
                ['user_id' => $userId],
                [
                    'price_updates_count' => 0,
                    'reviews_count' => 0,
                    'products_added_count' => 0,
                    'accurate_contributions_count' => 0,
                    'inaccurate_contributions_count' => 0,
                    'reputation_score' => 0,
                    'tier' => 'bronze',
                ]
            );

        $stats->reputation_score = max(
            0,
            (float) $stats->reputation_score + ($isValid ? 1 : -1)
        );

        if ($isValid) {
            $stats->price_updates_count++;
            $stats->accurate_contributions_count++;
            $stats->last_price_update_at = $timestamp;
        } else {
            $stats->inaccurate_contributions_count++;
        }

        $stats->save();
    }
}
