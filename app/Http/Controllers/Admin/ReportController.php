<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Product;
use App\Models\PriceContribution;
use App\Models\ProductMarketPrice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Contribution Analytics Report
     */
    public function contributions(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        // Daily contribution trend
        $dailyContributions = PriceContribution::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status breakdown
        $statusBreakdown = PriceContribution::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Top contributors
        $topContributors = PriceContribution::with('user')
            ->selectRaw('user_id, COUNT(*) as total_contributions, 
                         SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('total_contributions')
            ->limit(10)
            ->get();

        // Average approval time
        $avgApprovalTime = PriceContribution::whereNotNull('verified_at')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, verified_at)) as avg_hours')
            ->value('avg_hours');

        // Recent pending contributions
        $pendingContributions = PriceContribution::with(['user', 'product', 'market'])
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.reports.contributions', compact(
            'dailyContributions',
            'statusBreakdown',
            'topContributors',
            'avgApprovalTime',
            'pendingContributions',
            'period'
        ));
    }

    /**
     * Data Quality Report
     */
    public function dataQuality()
    {
        // Markets missing critical info
        $incompleteMarkets = Market::where(function($query) {
            $query->whereNull('phone')
                  ->orWhereNull('address')
                  ->orWhereNull('latitude')
                  ->orWhereNull('longitude');
        })->with('zone')->paginate(15, ['*'], 'markets_page');

        // Products without recent prices
        $productsWithoutPrices = Product::whereDoesntHave('marketPrices', function($query) {
            $query->where('updated_at', '>=', Carbon::now()->subDays(7));
        })->with('category')->paginate(15, ['*'], 'products_page');

        // Outdated prices
        $outdatedPrices = ProductMarketPrice::with(['product', 'market'])
            ->where('updated_at', '<', Carbon::now()->subDays(7))
            ->orderBy('updated_at')
            ->paginate(15, ['*'], 'prices_page');

        // Summary stats
        $stats = [
            'total_markets' => Market::count(),
            'incomplete_markets' => Market::where(function($query) {
                $query->whereNull('phone')
                      ->orWhereNull('address')
                      ->orWhereNull('latitude')
                      ->orWhereNull('longitude');
            })->count(),
            'total_products' => Product::count(),
            'products_without_prices' => Product::whereDoesntHave('marketPrices')->count(),
            'outdated_prices_count' => ProductMarketPrice::where('updated_at', '<', Carbon::now()->subDays(7))->count(),
            'active_contributors' => PriceContribution::where('created_at', '>=', Carbon::now()->subDays(30))
                ->distinct('user_id')
                ->count('user_id'),
        ];

        return view('admin.reports.data-quality', compact(
            'incompleteMarkets',
            'productsWithoutPrices',
            'outdatedPrices',
            'stats'
        ));
    }

    /**
     * Market Analytics Report
     */
    public function markets()
    {
        // Markets by division
        $marketsByDivision = Market::selectRaw('division, COUNT(*) as count')
            ->groupBy('division')
            ->orderByDesc('count')
            ->get();

        // Markets by type
        $marketsByType = Market::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderByDesc('count')
            ->get();

        // Active vs inactive
        $marketsByStatus = Market::selectRaw('is_active, COUNT(*) as count')
            ->groupBy('is_active')
            ->get();

        // Recent markets
        $recentMarkets = Market::with('zone')
            ->latest()
            ->limit(10)
            ->get();

        // Markets pending approval
        $pendingMarkets = Market::where('is_active', false)
            ->latest()
            ->limit(10)
            ->get();

        $stats = [
            'total' => Market::count(),
            'active' => Market::where('is_active', true)->count(),
            'inactive' => Market::where('is_active', false)->count(),
            'with_prices' => Market::has('marketPrices')->count(),
        ];

        return view('admin.reports.markets', compact(
            'marketsByDivision',
            'marketsByType',
            'marketsByStatus',
            'recentMarkets',
            'pendingMarkets',
            'stats'
        ));
    }

    /**
     * Price Analytics Report
     */
    public function prices(Request $request)
    {
        $productId = $request->get('product_id');
        $marketId = $request->get('market_id');

        // Price trends (last 30 days)
        $priceTrends = ProductMarketPrice::with(['product', 'market'])
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->when($marketId, fn($q) => $q->where('market_id', $marketId))
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(updated_at) as date, AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Most expensive markets
        $expensiveMarkets = ProductMarketPrice::with('market')
            ->selectRaw('market_id, AVG(price) as avg_price, COUNT(*) as product_count')
            ->groupBy('market_id')
            ->orderByDesc('avg_price')
            ->limit(10)
            ->get();

        // Price volatility (products with highest price variance)
        $volatileProducts = ProductMarketPrice::with('product')
            ->selectRaw('product_id, STDDEV(price) as price_stddev, AVG(price) as avg_price')
            ->groupBy('product_id')
            ->orderByDesc('price_stddev')
            ->limit(10)
            ->get();

        $stats = [
            'total_prices' => ProductMarketPrice::count(),
            'updated_today' => ProductMarketPrice::whereDate('updated_at', Carbon::today())->count(),
            'updated_this_week' => ProductMarketPrice::where('updated_at', '>=', Carbon::now()->subDays(7))->count(),
            'products_tracked' => ProductMarketPrice::distinct('product_id')->count('product_id'),
        ];

        return view('admin.reports.prices', compact(
            'priceTrends',
            'expensiveMarkets',
            'volatileProducts',
            'stats'
        ));
    }
}
