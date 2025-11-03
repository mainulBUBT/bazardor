<?php

namespace App\Services;

use App\Models\PriceContribution;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

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
            'verified_at' => now(),
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
            'verified_at' => null,
        ])->save();
    }
}
