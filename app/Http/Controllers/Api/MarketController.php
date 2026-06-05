<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompareMarketProductsRequest;
use App\Http\Requests\CompareMarketsRequest;
use App\Http\Resources\MarketResource;
use App\Http\Resources\MarketsComparisonResource;
use App\Http\Resources\ProductComparisonResource;
use App\Http\Resources\ProductResource;
use App\Services\MarketComparisonService;
use App\Services\MarketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketController extends Controller
{
    public function __construct(
        private MarketService $marketService,
        private MarketComparisonService $marketComparisonService
    ) {}

    /**
     * Get random markets by zone.
     */
    public function random(Request $request): JsonResponse
    {
        $zoneId = $request->attributes->get('zoneId');
        $limit = $request->limit ?? pagination_limit();

        $markets = $this->marketService->getMarkets()
            ->getCollection()
            ->filter(fn ($market) => $market->zone_id === $zoneId && $market->is_active && $market->visibility)
            ->shuffle()
            ->take($limit)
            ->values();

        return response()->json(formated_response(
            MARKET_200,
            MarketResource::collection($markets)
        ), 200);
    }

    /**
     * Get markets list with pagination, filters, distance, and operating hours.
     */
    public function index(Request $request): JsonResponse
    {
        $zoneId = $request->attributes->get('zoneId');
        $categoryId = $request->category_id;
        $limit = (int) ($request->limit ?? pagination_limit());
        $offset = (int) ($request->offset ?? 1);
        $userLat = (float) ($request->user_lat ?? 0);
        $userLng = (float) ($request->user_lng ?? 0);

        $markets = $this->marketService->getMarketsByZoneWithFilters(
            zoneId: $zoneId,
            categoryId: $categoryId,
            userLat: $userLat,
            userLng: $userLng,
            search: $request->search,
            isOpen: $request->is_open,
            type: $request->type,
            information: $request->information,
            limit: $limit,
            offset: $offset
        );

        return response()->json(formated_response(
            MARKET_200,
            MarketResource::collection($markets),
            $limit,
            $offset
        ), 200);
    }

    /**
     * Get detailed information about a specific market.
     */
    public function show(string $id, Request $request): JsonResponse
    {
        $zoneId = $request->attributes->get('zoneId');

        $market = $this->marketService->findById($id, [
            'marketInformation',
            'openingHours',
            'zone',
        ]);

        if ($market->zone_id !== $zoneId) {
            return response()->json(
                formated_response(constant: MARKET_NOT_FOUND_404),
                404
            );
        }

        return response()->json(formated_response(
            MARKET_200,
            new MarketResource($market)
        ), 200);
    }

    /**
     * Get paginated list of products available in a specific market.
     */
    public function products(string $id, Request $request): JsonResponse
    {
        $zoneId = $request->attributes->get('zoneId');
        $limit = (int) ($request->limit ?? pagination_limit());
        $offset = (int) ($request->offset ?? 1);
        $categoryId = $request->category_id;
        $search = $request->q;

        $market = $this->marketService->findById($id);

        if ($market->zone_id !== $zoneId) {
            return response()->json(
                formated_response(constant: MARKET_NOT_FOUND_404),
                404
            );
        }

        $products = $this->marketService->getMarketProducts(
            marketId: $id,
            categoryId: $categoryId,
            search: $search,
            limit: $limit,
            offset: $offset
        );

        return response()->json(formated_response(
            PRODUCT_200,
            ProductResource::collection($products),
            $limit,
            $offset
        ), 200);
    }

    /**
     * Compare two markets - details, features, and statistics.
     */
    public function compare(CompareMarketsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $comparison = $this->marketComparisonService->compareMarkets(
            marketId1: $validated['market_id_1'],
            marketId2: $validated['market_id_2'],
            userLat: $validated['user_lat'] ?? null,
            userLng: $validated['user_lng'] ?? null
        );

        return response()->json(formated_response(
            MARKET_COMPARISON_200,
            new MarketsComparisonResource($comparison)
        ), 200);
    }

    /**
     * Compare products between two markets.
     */
    public function compareProducts(CompareMarketProductsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $limit = (int) ($validated['limit'] ?? pagination_limit());
        $offset = (int) ($validated['offset'] ?? 1);

        $comparisonProducts = $this->marketComparisonService->compareMarketProducts(
            marketId1: $validated['market_id_1'],
            marketId2: $validated['market_id_2'],
            categoryId: $validated['category_id'] ?? null,
            limit: $limit,
            offset: $offset
        );

        return response()->json(formated_response(
            MARKET_PRODUCTS_COMPARISON_200,
            ProductComparisonResource::collection($comparisonProducts),
            $limit,
            $offset
        ), 200);
    }
}
