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

    /**
     * Submit a price contribution for a user with rate limiting.
     */
    public function submitPrice(User $user, array $data, ?UploadedFile $proof = null): array
    {
        $lastContribution = $this->priceContribution
            ->where('user_id', $user->id)
            ->where('product_id', $data['product_id'])
            ->where('market_id', $data['market_id'])
            ->latest('updated_at')
            ->first();

        $lastSubmissionAt = $lastContribution?->updated_at ?? $lastContribution?->created_at;

        if ($lastSubmissionAt && $lastSubmissionAt->greaterThan(now()->subHour())) {
            return [
                'rate_limited' => true,
                'last_submission_at' => $lastSubmissionAt->toIso8601String(),
                'contribution' => null,
            ];
        }

        $proofPath = $lastContribution?->proof_image;

        if ($proof) {
            $proofPath = handle_file_upload(
                dir: 'price-contributions',
                format: $proof->getClientOriginalExtension(),
                newFile: $proof,
                oldFile: $lastContribution?->proof_image
            );
        }

        $contribution = $this->priceContribution->updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $data['product_id'],
                'market_id' => $data['market_id'],
            ],
            [
                'submitted_price' => $data['submitted_price'],
                'proof_image' => $proofPath,
                'status' => 'pending',
            ]
        )->fresh();

        return [
            'rate_limited' => false,
            'last_submission_at' => $contribution->updated_at?->toIso8601String(),
            'contribution' => $contribution,
        ];
    }
}
