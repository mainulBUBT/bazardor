<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Product;
use App\Models\PriceContribution;
use App\Models\PriceContributionHistory;
use App\Models\ProductMarketPrice;
use App\Models\User;
use App\Models\UserStatistics;
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
        $status = $request->get('status'); // pending, validated, invalid
        $userId = $request->get('user_id');

        // Daily contribution trend
        $dailyContributions = PriceContribution::where('created_at', '>=', $startDate)
            ->when($status === 'pending', fn($q) => $q->where('status', 'pending'))
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status breakdown (from history)
        $statusBreakdown = PriceContributionHistory::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Add pending count from active contributions
        $statusBreakdown['pending'] = PriceContribution::where('status', 'pending')->count();

        // Top contributors (from history)
        $topContributors = PriceContributionHistory::with('user')
            ->selectRaw('user_id, COUNT(*) as total_contributions, 
                         SUM(CASE WHEN status = "validated" THEN 1 ELSE 0 END) as validated_count')
            ->where('created_at', '>=', $startDate)
            ->when($status && $status !== 'pending', fn($q) => $q->where('status', $status))
            ->groupBy('user_id')
            ->orderByDesc('total_contributions')
            ->limit(10)
            ->get();

        // Average processing time (from history)
        $avgApprovalTime = PriceContributionHistory::whereNotNull('validated_at')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, validated_at)) as avg_hours')
            ->value('avg_hours');

        // Recent pending contributions
        $pendingContributions = PriceContribution::with(['user', 'product', 'market'])
            ->where('status', 'pending')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->limit(10)
            ->get();

        // Accuracy rate (validated vs invalid)
        $totalProcessed = PriceContributionHistory::where('created_at', '>=', $startDate)->count();
        $validatedCount = PriceContributionHistory::where('status', 'validated')
            ->where('created_at', '>=', $startDate)
            ->count();
        $accuracyRate = $totalProcessed > 0 ? round(($validatedCount / $totalProcessed) * 100, 2) : 0;

        // Contribution velocity (contributions per day)
        $contributionVelocity = $period > 0 ? round($totalProcessed / $period, 2) : 0;

        // Most active products (by contribution count)
        $mostContributedProducts = PriceContributionHistory::with('product')
            ->selectRaw('product_id, COUNT(*) as contribution_count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('product_id')
            ->orderByDesc('contribution_count')
            ->limit(5)
            ->get();

        // Most active markets (by contribution count)
        $mostContributedMarkets = PriceContributionHistory::with('market')
            ->selectRaw('market_id, COUNT(*) as contribution_count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('market_id')
            ->orderByDesc('contribution_count')
            ->limit(5)
            ->get();

        return view('admin.reports.contributions', compact(
            'dailyContributions',
            'statusBreakdown',
            'topContributors',
            'avgApprovalTime',
            'pendingContributions',
            'period',
            'accuracyRate',
            'contributionVelocity',
            'mostContributedProducts',
            'mostContributedMarkets',
            'totalProcessed',
            'validatedCount',
            'status',
            'userId'
        ));
    }

    /**
     * Data Quality Report
     */
    public function dataQuality(Request $request)
    {
        $issueType = $request->get('issue_type'); // incomplete_markets, missing_prices, outdated_prices
        // Markets missing critical info
        $incompleteMarkets = Market::where(function($query) {
            $query->whereNull('phone')
                  ->orWhereNull('address')
                  ->orWhereNull('latitude')
                  ->orWhereNull('longitude');
        })
        ->when($issueType && $issueType !== 'incomplete_markets', fn($q) => $q->limit(0))
        ->with('zone')
        ->paginate(15, ['*'], 'markets_page');

        // Products without recent prices
        $productsWithoutPrices = Product::whereDoesntHave('marketPrices', function($query) {
            $query->where('updated_at', '>=', Carbon::now()->subDays(7));
        })
        ->when($issueType && $issueType !== 'missing_prices', fn($q) => $q->limit(0))
        ->with('category')
        ->paginate(15, ['*'], 'products_page');

        // Outdated prices
        $outdatedPrices = ProductMarketPrice::with(['product', 'market'])
            ->where('updated_at', '<', Carbon::now()->subDays(7))
            ->when($issueType && $issueType !== 'outdated_prices', fn($q) => $q->limit(0))
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
            'products_with_recent_prices' => Product::whereHas('marketPrices', function($query) {
                $query->where('updated_at', '>=', Carbon::now()->subDays(7));
            })->count(),
            'price_coverage_percentage' => Product::count() > 0 
                ? round((Product::has('marketPrices')->count() / Product::count()) * 100, 2) 
                : 0,
            'avg_prices_per_product' => Product::has('marketPrices')->count() > 0
                ? round(ProductMarketPrice::count() / Product::has('marketPrices')->count(), 2)
                : 0,
        ];

        return view('admin.reports.data-quality', compact(
            'incompleteMarkets',
            'productsWithoutPrices',
            'outdatedPrices',
            'stats',
            'issueType'
        ));
    }

    /**
     * Market Analytics Report
     */
    public function markets(Request $request)
    {
        $division = $request->get('division');
        $status = $request->get('status'); // active, inactive
        // Markets by division
        $marketsByDivision = Market::selectRaw('division, COUNT(*) as count')
            ->when($status !== null, fn($q) => $q->where('is_active', $status === 'active'))
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
            ->when($division, fn($q) => $q->where('division', $division))
            ->when($status !== null, fn($q) => $q->where('is_active', $status === 'active'))
            ->latest()
            ->limit(10)
            ->get();

        // Markets pending approval
        $pendingMarkets = Market::where('is_active', false)
            ->latest()
            ->limit(10)
            ->get();

        // Markets with most products tracked
        $marketsWithMostProducts = ProductMarketPrice::with('market')
            ->selectRaw('market_id, COUNT(DISTINCT product_id) as product_count')
            ->groupBy('market_id')
            ->orderByDesc('product_count')
            ->limit(10)
            ->get();

        // Markets by district (top 10)
        $marketsByDistrict = Market::selectRaw('district, COUNT(*) as count')
            ->whereNotNull('district')
            ->groupBy('district')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $stats = [
            'total' => Market::count(),
            'active' => Market::where('is_active', true)->count(),
            'inactive' => Market::where('is_active', false)->count(),
            'with_prices' => Market::has('marketPrices')->count(),
            'without_prices' => Market::doesntHave('marketPrices')->count(),
            'avg_products_per_market' => Market::has('marketPrices')->count() > 0
                ? round(ProductMarketPrice::selectRaw('market_id, COUNT(DISTINCT product_id) as cnt')
                    ->groupBy('market_id')
                    ->get()
                    ->avg('cnt'), 2)
                : 0,
        ];

        return view('admin.reports.markets', compact(
            'marketsByDivision',
            'marketsByType',
            'marketsByStatus',
            'recentMarkets',
            'pendingMarkets',
            'stats',
            'marketsWithMostProducts',
            'marketsByDistrict',
            'division',
            'status'
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

        // Price change analysis (products with significant price changes)
        $significantPriceChanges = DB::table('product_market_prices as pmp1')
            ->join('product_market_prices as pmp2', function($join) {
                $join->on('pmp1.product_id', '=', 'pmp2.product_id')
                     ->on('pmp1.market_id', '=', 'pmp2.market_id');
            })
            ->join('products', 'pmp1.product_id', '=', 'products.id')
            ->join('markets', 'pmp1.market_id', '=', 'markets.id')
            ->whereRaw('pmp1.updated_at > pmp2.updated_at')
            ->whereRaw('pmp1.updated_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')
            ->whereRaw('ABS((pmp1.price - pmp2.price) / pmp2.price * 100) > 10')
            ->selectRaw('pmp1.product_id, pmp1.market_id, products.name as product_name, 
                         markets.name as market_name, pmp2.price as old_price, pmp1.price as new_price,
                         ((pmp1.price - pmp2.price) / pmp2.price * 100) as change_percentage')
            ->orderByRaw('ABS(change_percentage) DESC')
            ->limit(10)
            ->get();

        // Products with stable prices (low variance)
        $stableProducts = ProductMarketPrice::with('product')
            ->selectRaw('product_id, STDDEV(price) as price_stddev, AVG(price) as avg_price, COUNT(*) as market_count')
            ->groupBy('product_id')
            ->having('market_count', '>=', 3)
            ->orderBy('price_stddev')
            ->limit(10)
            ->get();

        $stats = [
            'total_prices' => ProductMarketPrice::count(),
            'updated_today' => ProductMarketPrice::whereDate('updated_at', Carbon::today())->count(),
            'updated_this_week' => ProductMarketPrice::where('updated_at', '>=', Carbon::now()->subDays(7))->count(),
            'products_tracked' => ProductMarketPrice::distinct('product_id')->count('product_id'),
            'markets_tracked' => ProductMarketPrice::distinct('market_id')->count('market_id'),
            'avg_price_updates_per_day' => ProductMarketPrice::where('updated_at', '>=', Carbon::now()->subDays(30))
                ->count() / 30,
            'price_entries_last_30_days' => ProductMarketPrice::where('updated_at', '>=', Carbon::now()->subDays(30))->count(),
        ];

        return view('admin.reports.prices', compact(
            'priceTrends',
            'expensiveMarkets',
            'volatileProducts',
            'stats',
            'significantPriceChanges',
            'stableProducts'
        ));
    }
}
