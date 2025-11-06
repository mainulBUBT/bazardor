<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryMarketResource;
use App\Http\Resources\MarketResource;
use App\Http\Resources\ProductResource;
use App\Services\CategoryService;
use App\Services\MarketService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CategoryMarketController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
        private MarketService $marketService,
        private ProductService $productService
    ) {
    }


    /**
     * Summary of list
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getCategoriesWithMarketCountsList(Request $request) : array|JsonResponse
    {
        if (!$request->hasHeader('zoneId')) {
            return response()->json(
                formated_response(constant: ZONE_ID_REQUIRED_403),
                403
            );
        }
        
        $zoneId = $request->header('zoneId');
        $limit = $request->limit ?? pagination_limit();
        $offset = $request->offset ?? 1;

        $categories = $this->categoryService->getCategoriesWithMarketCounts($zoneId, $limit, $offset);
        $resource = CategoryMarketResource::collection($categories);

        return response()->json(formated_response(CATEGORY_MARKET_LIST_200, $resource, $limit, $offset), 200);
   }

    /**
     * Get random markets list by zone
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRandomMarketsList(Request $request): JsonResponse
    {
        if (!$request->hasHeader('zoneId')) {
            return response()->json(
                formated_response(constant: ZONE_ID_REQUIRED_403),
                403
            );
        }
        
        $zoneId = $request->header('zoneId');
        $limit = $request->limit ?? pagination_limit();

        // Get random markets by zone with efficient query
        $markets = $this->marketService->getMarkets()
            ->getCollection()
            ->filter(fn($market) => $market->zone_id === $zoneId && $market->is_active && $market->visibility)
            ->shuffle()
            ->take($limit)
            ->values();

        return response()->json(formated_response(
            MARKET_200,
            MarketResource::collection($markets)
        ), 200);
    }

    /**
     * Get random products available in markets for a zone
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getRandomProductList(Request $request): JsonResponse
    {
        if (!$request->hasHeader('zoneId')) {
            return response()->json(
                formated_response(constant: ZONE_ID_REQUIRED_403),
                403
            );
        }

        $zoneId = $request->header('zoneId');
        $limit = (int) ($request->limit ?? pagination_limit());

        $products = $this->productService->getRandomProductsByZone($zoneId, $limit);

        return response()->json(formated_response(
            PRODUCT_200,
            ProductResource::collection($products)
        ), 200);
    }

    /**
     * Get markets list with pagination, filters, distance, and operating hours
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMarketsList(Request $request): JsonResponse
    {
        if (!$request->hasHeader('zoneId')) {
            return response()->json(
                formated_response(constant: ZONE_ID_REQUIRED_403),
                403
            );
        }

        $zoneId = $request->header('zoneId');
        $limit = (int) ($request->limit ?? pagination_limit());
        $offset = (int) ($request->offset ?? 1);
        $userLat = (float) ($request->user_lat ?? 0);
        $userLng = (float) ($request->user_lng ?? 0);

        $markets = $this->marketService->getMarketsByZoneWithFilters(
            zoneId: $zoneId,
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
}
