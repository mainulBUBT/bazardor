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

            [$valid, $invalid] = $this->partitionContributions($contributions, $threshold);

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
            }

            $historyPayload = [];

            foreach ($contributions as $contribution) {
                $isValid = $valid->contains(fn (PriceContribution $item) => $item->id === $contribution->id);

                $this->updateUserStatistics(
                    userId: $contribution->user_id,
                    isValid: $isValid,
                    timestamp: $timestamp
                );

                $historyPayload[] = [
                    'id' => $contribution->id,
                    'product_id' => $contribution->product_id,
                    'market_id' => $contribution->market_id,
                    'user_id' => $contribution->user_id,
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

    private function partitionContributions(EloquentCollection $contributions, ?PriceThreshold $threshold): array
    {
        $valid = new Collection();
        $invalid = new Collection();

        foreach ($contributions as $contribution) {
            if ($this->isContributionRealistic($contribution, $threshold)) {
                $valid->push($contribution);
            } else {
                $invalid->push($contribution);
            }
        }

        return [$valid, $invalid];
    }

    private function isContributionRealistic(PriceContribution $contribution, ?PriceThreshold $threshold): bool
    {
        if ($contribution->submitted_price <= 0) {
            return false;
        }

        if ($threshold === null) {
            return true;
        }

        return $contribution->submitted_price >= $threshold->min_price
            && $contribution->submitted_price <= $threshold->max_price;
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
