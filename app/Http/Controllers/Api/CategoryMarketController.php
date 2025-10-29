<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryMarketResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CategoryMarketController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }


    /**
     * Summary of list
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getCategoriesList(Request $request) : array|JsonResponse
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

        return formated_response(constant: CATEGORY_MARKET_LIST_200, content: $resource, limit: $limit, offset: $offset);
    }
}
