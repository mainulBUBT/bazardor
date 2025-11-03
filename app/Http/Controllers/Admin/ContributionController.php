<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission;
use App\Http\Controllers\Controller;
use App\Models\PriceContribution;
use App\Services\ContributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Brian2694\Toastr\Facades\Toastr;

class ContributionController extends Controller
{
    public function __construct(private ContributionService $contributionService)
    {
    }

    /**
     * Display the contributions dashboard listing.
     */
    public function index(Request $request): View
    {
        $filters = $request->only('status');
        $contributions = $this->contributionService->paginate($filters);
        $stats = $this->contributionService->getStats();

        return view('admin.contributions.index', [
            'contributions' => $contributions,
            'stats' => $stats,
        ]);
    }

    /**
     * Approve a pending contribution.
     */
    public function approve(PriceContribution $contribution): RedirectResponse
    {
        $this->contributionService->approve($contribution);

        Toastr::success(translate('messages.contribution_approved_successfully'));

        return back();
    }

    /**
     * Reject a pending contribution.
     */
    public function reject(PriceContribution $contribution): RedirectResponse
    {
        $this->contributionService->reject($contribution);

        Toastr::info(translate('messages.contribution_rejected_successfully'));

        return back();
    }
}
