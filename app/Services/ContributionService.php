<?php

namespace App\Services;

use App\Models\PriceContribution;
use App\Models\PriceContributionHistory;
use App\Models\ProductMarketPrice;
use App\Models\User;
use App\Models\UserStatistics;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ContributionService
{
    public function __construct(
        private PriceContribution $priceContribution,
        private SettingService $settingService
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $status = Arr::get($filters, 'status');

        return $this->priceContribution
            ->with(['user', 'product', 'market'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function getStats(): array
    {
        return [
            'total' => $this->priceContribution->count(),
            'pending' => $this->priceContribution->where('status', 'pending')->count(),
            'approved' => $this->priceContribution->where('status', 'approved')->count(),
            'contributors' => $this->priceContribution->distinct('user_id')->count('user_id'),
        ];
    }

    public function approve(PriceContribution $contribution): void
    {
        if ($contribution->status === 'approved') {
            return;
        }

        DB::transaction(function () use ($contribution) {
            $contribution->forceFill(['status' => 'approved'])->save();

            ProductMarketPrice::query()->updateOrCreate(
                [
                    'product_id' => $contribution->product_id,
                    'market_id' => $contribution->market_id,
                ],
                [
                    'price' => round((float) $contribution->submitted_price, 2),
                    'price_date' => now(),
                ]
            );

            if ($contribution->user_id) {
                $this->updateUserReputation($contribution->user_id, true);
            }

            $this->archiveContribution($contribution, 'validated');
        });
    }

    public function reject(PriceContribution $contribution): void
    {
        if ($contribution->status === 'rejected') {
            return;
        }

        DB::transaction(function () use ($contribution) {
            $contribution->forceFill(['status' => 'rejected'])->save();

            if ($contribution->user_id) {
                $this->updateUserReputation($contribution->user_id, false);
            }

            $this->archiveContribution($contribution, 'invalid');
        });
    }

    public function submitPrice(?User $user, ?string $deviceId, array $data): array
    {
        $rateLimitKey = $user?->id ?? $deviceId;

        if ($rateLimitKey) {
            $rateLimited = $this->checkRateLimit($user, $deviceId, $data);

            if ($rateLimited) {
                return $rateLimited;
            }
        }

        $reference = $this->getReferencePrice($data['product_id'], $data['market_id']);

        if (! $this->passesGateCheck((float) $data['submitted_price'], $reference)) {
            return [
                'rate_limited' => false,
                'last_submission_at' => now()->toIso8601String(),
                'contribution' => null,
            ];
        }

        $criteria = [
            'product_id' => $data['product_id'],
            'market_id' => $data['market_id'],
        ];

        if ($user) {
            $criteria['user_id'] = $user->id;
        } elseif ($deviceId) {
            $criteria['device_id'] = $deviceId;
        }

        $contribution = $this->priceContribution->updateOrCreate(
            $criteria,
            [
                'submitted_price' => $data['submitted_price'],
                'status' => 'pending',
                'user_id' => $user?->id,
                'device_id' => $user ? null : $deviceId,
            ]
        )->fresh();

        $this->archiveOldContributions($data['product_id'], $data['market_id']);

        $this->recomputeAndUpdatePrice($data['product_id'], $data['market_id']);

        return [
            'rate_limited' => false,
            'last_submission_at' => $contribution->updated_at?->toIso8601String(),
            'contribution' => $contribution,
        ];
    }

    private function checkRateLimit(?User $user, ?string $deviceId, array $data): ?array
    {
        $rateLimitMinutes = (int) $this->settingService->getSettingWithDefault('rate_limit_minutes', 'business') ?? 60;

        $query = $this->priceContribution
            ->where('product_id', $data['product_id'])
            ->where('market_id', $data['market_id'])
            ->latest('updated_at');

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('device_id', $deviceId);
        }

        $lastContribution = $query->first();
        $lastSubmissionAt = $lastContribution?->updated_at ?? $lastContribution?->created_at;

        if ($lastSubmissionAt && $lastSubmissionAt->greaterThan(now()->subMinutes($rateLimitMinutes))) {
            return [
                'rate_limited' => true,
                'last_submission_at' => $lastSubmissionAt->toIso8601String(),
                'contribution' => null,
            ];
        }

        return null;
    }

    private function getReferencePrice(string $productId, string $marketId): ?float
    {
        $current = ProductMarketPrice::query()
            ->where('product_id', $productId)
            ->where('market_id', $marketId)
            ->value('price');

        if ($current !== null) {
            return (float) $current;
        }

        $zoneId = DB::table('markets')->where('id', $marketId)->value('zone_id');

        if ($zoneId) {
            $zonePrices = DB::table('product_market_prices')
                ->join('markets', 'markets.id', '=', 'product_market_prices.market_id')
                ->where('product_market_prices.product_id', $productId)
                ->where('markets.zone_id', $zoneId)
                ->pluck('product_market_prices.price')
                ->map(fn ($p) => (float) $p)
                ->sort()
                ->values();

            if ($zonePrices->isNotEmpty()) {
                return compute_median($zonePrices);
            }
        }

        $basePrice = DB::table('products')->where('id', $productId)->value('base_price');

        return $basePrice ? (float) $basePrice : null;
    }

    private function passesGateCheck(float $submitted, ?float $reference): bool
    {
        if ($reference === null || $reference <= 0) {
            return $submitted > 0;
        }

        $tolerance = (float) $this->settingService->getSettingWithDefault('price_tolerance', 'business') ?? 0.50;
        $min = $reference * (1 - $tolerance);
        $max = $reference * (1 + $tolerance);

        return $submitted >= $min && $submitted <= $max;
    }

    private function recomputeAndUpdatePrice(string $productId, string $marketId): void
    {
        $windowHours = (int) $this->settingService->getSettingWithDefault('contribution_window_hours', 'business') ?? 24;
        $minSubmissions = (int) $this->settingService->getSettingWithDefault('min_submissions_for_median', 'business') ?? 1;

        $prices = PriceContribution::query()
            ->where('product_id', $productId)
            ->where('market_id', $marketId)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHours($windowHours))
            ->pluck('submitted_price')
            ->map(fn ($p) => (float) $p)
            ->sort()
            ->values();

        if ($prices->count() < $minSubmissions) {
            return;
        }

        $median = compute_median($prices);

        ProductMarketPrice::query()->updateOrCreate(
            ['product_id' => $productId, 'market_id' => $marketId],
            ['price' => round($median, 2), 'price_date' => now()]
        );
    }

    private function archiveOldContributions(string $productId, string $marketId): void
    {
        $windowHours = (int) $this->settingService->getSettingWithDefault('contribution_window_hours', 'business') ?? 24;

        $old = PriceContribution::query()
            ->where('product_id', $productId)
            ->where('market_id', $marketId)
            ->where('created_at', '<', now()->subHours($windowHours))
            ->get();

        if ($old->isEmpty()) {
            return;
        }

        $payload = $old->map(fn ($c) => [
            'id' => $c->id,
            'product_id' => $c->product_id,
            'market_id' => $c->market_id,
            'user_id' => $c->user_id,
            'device_id' => $c->device_id,
            'submitted_price' => $c->submitted_price,
            'proof_image' => $c->proof_image,
            'status' => 'validated',
            'validated_at' => now(),
            'created_at' => $c->created_at,
            'updated_at' => now(),
        ])->toArray();

        PriceContributionHistory::query()->upsert($payload, ['id']);

        PriceContribution::query()
            ->whereIn('id', $old->pluck('id'))
            ->forceDelete();
    }

    private function archiveContribution(PriceContribution $contribution, string $status): void
    {
        PriceContributionHistory::query()->upsert(
            [
                'id' => $contribution->id,
                'product_id' => $contribution->product_id,
                'market_id' => $contribution->market_id,
                'user_id' => $contribution->user_id,
                'device_id' => $contribution->device_id,
                'submitted_price' => $contribution->submitted_price,
                'proof_image' => $contribution->proof_image,
                'status' => $status,
                'validated_at' => now(),
                'created_at' => $contribution->created_at,
                'updated_at' => now(),
            ],
            ['id']
        );

        PriceContribution::query()->where('id', $contribution->id)->forceDelete();
    }

    private function updateUserReputation(string $userId, bool $isValid): void
    {
        $stats = UserStatistics::query()->firstOrCreate(
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
            $stats->last_price_update_at = now();
        } else {
            $stats->inaccurate_contributions_count++;
        }

        $stats->save();
    }
}
