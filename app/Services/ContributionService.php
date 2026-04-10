<?php

namespace App\Services;

use App\Models\PriceContribution;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;

class ContributionService
{
    public function __construct(private PriceContribution $priceContribution)
    {
    }

    /**
     * Paginate contributions with optional filters.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $status = Arr::get($filters, 'status');

        return $this->priceContribution
            ->with(['user', 'product', 'market'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Aggregate contribution statistics.
     */
    public function getStats(): array
    {
        return [
            'total' => $this->priceContribution->count(),
            'pending' => $this->priceContribution->where('status', 'pending')->count(),
            'approved' => $this->priceContribution->where('status', 'approved')->count(),
            'contributors' => $this->priceContribution->distinct('user_id')->count('user_id'),
        ];
    }

    /**
     * Approve a contribution.
     */
    public function approve(PriceContribution $contribution): void
    {
        if ($contribution->status === 'approved') {
            return;
        }

        $contribution->forceFill([
            'status' => 'approved',
        ])->save();
    }

    /**
     * Reject a contribution.
     */
    public function reject(PriceContribution $contribution): void
    {
        if ($contribution->status === 'rejected') {
            return;
        }

        $contribution->forceFill([
            'status' => 'rejected',
        ])->save();
    }

    /**
     * Submit a price contribution with optional authentication.
     */
    public function submitPrice(?User $user, ?string $deviceId, array $data): array
    {
        $rateLimitKey = $user?->id ?? $deviceId;

        // Rate limiting for both authenticated and guest users
        if ($rateLimitKey) {
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

            if ($lastSubmissionAt && $lastSubmissionAt->greaterThan(now()->subHour())) {
                return [
                    'rate_limited' => true,
                    'last_submission_at' => $lastSubmissionAt->toIso8601String(),
                    'contribution' => null,
                ];
            }
        }

        // Build criteria for updateOrCreate
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

        return [
            'rate_limited' => false,
            'last_submission_at' => $contribution->updated_at?->toIso8601String(),
            'contribution' => $contribution,
        ];
    }
}
