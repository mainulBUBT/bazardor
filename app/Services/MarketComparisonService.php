<?php

namespace App\Services;

use App\Models\Market;
use App\Models\ProductMarketPrice;
use Illuminate\Pagination\LengthAwarePaginator;

class MarketComparisonService
{
    public function __construct(
        private MarketService $marketService
    ) {
    }

    /**
     * Compare two markets with all details, features, and statistics
     * 
     * @param string $marketId1
     * @param string $marketId2
     * @param float|null $userLat
     * @param float|null $userLng
     * @return array
     */
    public function compareMarkets(string $marketId1, string $marketId2, ?float $userLat = null, ?float $userLng = null): array
    {
        // Get both markets with all necessary relationships
        $market1 = $this->marketService->findById($marketId1, [
            'marketInformation',
            'openingHours',
        ]);

        $market2 = $this->marketService->findById($marketId2, [
            'marketInformation',
            'openingHours',
        ]);

        return [
            'market_1' => $this->buildMarketComparisonData($market1, $userLat, $userLng),
            'market_2' => $this->buildMarketComparisonData($market2, $userLat, $userLng),
        ];
    }

    /**
     * Build comparison data for a single market
     * 
     * @param Market $market
     * @param float|null $userLat
     * @param float|null $userLng
     * @return array
     */
    private function buildMarketComparisonData(Market $market, ?float $userLat, ?float $userLng): array
    {
        // Calculate distance if user location provided
        $distance = null;
        if ($userLat && $userLng) {
            $distance = $market->getDistanceFrom($userLat, $userLng);
        }

        return [
            'id' => $market->id,
            'name' => $market->name,
            'type' => $market->type,
            'address' => $market->address,
            'distance_km' => $distance,
            'active_products_count' => $market->getActiveProductsCount(),
            'open_days_count' => $market->getOpenDaysCount(),
            'features' => [
                'non_veg_available' => (bool) ($market->marketInformation->is_non_veg ?? false),
                'halal_available' => (bool) ($market->marketInformation->is_halal ?? false),
                'parking_available' => (bool) ($market->marketInformation->is_parking ?? false),
                'restroom_available' => (bool) ($market->marketInformation->is_restroom ?? false),
                'home_delivery' => (bool) ($market->marketInformation->is_home_delivery ?? false),
            ],
            'opening_hours' => $market->openingHours->map(function ($hour) {
                return [
                    'day' => $hour->day,
                    'is_closed' => (bool) $hour->is_closed,
                    'opening' => $hour->opening,
                    'closing' => $hour->closing,
                ];
            }),
        ];
    }

    /**
     * Compare products between two markets
     * 
     * @param string $marketId1
     * @param string $marketId2
     * @param string|null $categoryId
     * @param int $limit
     * @param int $offset
     * @return LengthAwarePaginator
     */
    public function compareMarketProducts(
        string $marketId1,
        string $marketId2,
        ?string $categoryId = null,
        int $limit = 15,
        int $offset = 1
    ): LengthAwarePaginator {
        // Get products from market 1
        $market1Products = $this->getMarketProducts($marketId1, $categoryId, $limit, $offset);
        
        // Get products from market 2 (all, for comparison)
        $market2Products = $this->getMarketProducts($marketId2, $categoryId)
            ->keyBy('product_id');

        // Build comparison data
        $comparisonProducts = $market1Products->getCollection()->map(function ($price1) use ($market2Products) {
            return $this->buildProductComparisonData($price1, $market2Products);
        });

        $market1Products->setCollection($comparisonProducts);

        return $market1Products;
    }

    /**
     * Get products for a specific market
     * 
     * @param string $marketId
     * @param string|null $categoryId
     * @param int|null $limit
     * @param int|null $offset
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    private function getMarketProducts(string $marketId, ?string $categoryId = null, ?int $limit = null, ?int $offset = null)
    {
        $query = ProductMarketPrice::with(['product.category', 'product.unit'])
            ->where('market_id', $marketId)
            ->whereHas('product', function ($q) use ($categoryId) {
                $q->where('status', 'active')->where('is_visible', true);
                if ($categoryId) {
                    $q->where('category_id', $categoryId);
                }
            })
            ->latest('price_date');

        if ($limit && $offset) {
            return $query->paginate($limit, ['*'], 'page', $offset);
        }

        return $query->get();
    }

    /**
     * Build comparison data for a single product
     * 
     * @param ProductMarketPrice $price1
     * @param \Illuminate\Support\Collection $market2Products
     * @return array
     */
    private function buildProductComparisonData($price1, $market2Products): array
    {
        $product = $price1->product;
        $price2 = $market2Products->get($product->id);

        $comparison = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'category' => $product->category->name ?? null,
            'unit' => $product->unit->symbol ?? null,
            'market_1' => [
                'price' => (float) $price1->price,
                'price_date' => $price1->price_date,
                'available' => true,
            ],
            'market_2' => [
                'price' => $price2 ? (float) $price2->price : null,
                'price_date' => $price2 ? $price2->price_date : null,
                'available' => (bool) $price2,
            ],
        ];

        // Calculate price difference if product exists in both markets
        if ($price2) {
            $comparison['price_difference'] = $this->calculatePriceDifference(
                (float) $price1->price,
                (float) $price2->price
            );
        } else {
            $comparison['price_difference'] = null;
        }

        return $comparison;
    }

    /**
     * Calculate price difference between two prices
     * 
     * @param float $price1
     * @param float $price2
     * @return array
     */
    private function calculatePriceDifference(float $price1, float $price2): array
    {
        $difference = $price1 - $price2;
        $percentageDiff = $price2 > 0 
            ? round(($difference / $price2) * 100, 2) 
            : 0;

        return [
            'amount' => round($difference, 2),
            'percentage' => $percentageDiff,
            'cheaper_market' => $difference > 0 ? 'market_2' : ($difference < 0 ? 'market_1' : 'same'),
        ];
    }
}
