<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Market;
use App\Models\Product;
use App\Models\PriceContribution;
use App\Models\ProductMarketPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Quick overview stats
        $stats = [
            'total_markets' => Market::count(),
            'active_markets' => Market::where('is_active', true)->count(),
            'total_products' => Product::count(),
            'total_prices' => ProductMarketPrice::count(),
            'pending_contributions' => PriceContribution::where('status', 'pending')->count(),
            'todays_contributions' => PriceContribution::whereDate('created_at', Carbon::today())->count(),
            'total_users' => User::count(),
            'total_volunteers' => User::where('user_type', 'volunteer')->count(),
        ];

        // Quick actions - Pending items that need attention
        $pendingContributions = PriceContribution::with(['user', 'product', 'market'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        // Incomplete markets (missing critical info)
        $incompleteMarkets = Market::where(function($query) {
            $query->whereNull('phone')
                  ->orWhereNull('address')
                  ->orWhereNull('latitude');
        })->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'pendingContributions',
            'incompleteMarkets'
        ));
    }
}
