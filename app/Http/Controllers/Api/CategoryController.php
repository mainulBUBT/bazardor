<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryMarketResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * List categories with optional filters.
     */
    public function index(Request $request): array|JsonResponse
    {
        $zoneId = $request->attributes->get('zoneId');
        $limit = $request->limit ?? pagination_limit();
        $offset = $request->offset ?? 1;
        $search = $request->search;
        $minProducts = $request->filled('min_products') ? (int) $request->min_products : null;
        $minMarkets = $request->filled('min_markets') ? (int) $request->min_markets : null;

        $categories = $this->categoryService->getCategoriesList(
            zoneId: $zoneId,
            limit: $limit,
            offset: $offset,
            search: $search,
            minProducts: $minProducts,
            minMarkets: $minMarkets
        );
        $resource = CategoryMarketResource::collection($categories);

        return response()->json(formated_response(CATEGORY_MARKET_LIST_200, $resource, $limit, $offset), 200);
    }

    /**
     * Get a single category with market and product counts.
     */
    public function show(Request $request): JsonResponse
    {
        $categoryId = $request->id;
        if (! $categoryId) {
            return response()->json(
                formated_response(
                    ['response_code' => 'category_id_required', 'message' => 'Category ID is required'],
                    ['error' => 'Category ID is required']
                ),
                400
            );
        }

        $zoneId = $request->attributes->get('zoneId');
        $category = $this->categoryService->getCategoryById($categoryId, $zoneId);

        if (! $category) {
            return response()->json(
                formated_response(
                    ['response_code' => 'category_not_found', 'message' => 'Category not found'],
                    ['error' => 'Category not found']
                ),
                404
            );
        }

        return response()->json(formated_response(
            CATEGORY_MARKET_LIST_200,
            new CategoryMarketResource($category)
        ), 200);
    }
}
