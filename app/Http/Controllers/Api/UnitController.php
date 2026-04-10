<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UnitService;
use App\Http\Resources\UnitResource;
use Illuminate\Http\JsonResponse;

class UnitController extends Controller
{
    public function __construct(protected UnitService $unitService)
    {
        
    }

    /**
     * Get list of all units.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getUnitsList(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->query('search'),
            'unit_type' => $request->query('unit_type'),
            'is_active' => $request->query('is_active', true) // Default to active units for public API
        ];

        $getUnits = $this->unitService->getUnits($filters);
        $collection = UnitResource::collection($getUnits);

        return response()->json(formated_response(
            UNIT_LIST_200, 
            content: $collection, 
            limit: $request->limit ?? pagination_limit(), 
            offset: $request->offset ?? 1
        ));
    }
}
