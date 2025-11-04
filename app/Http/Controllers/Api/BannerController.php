<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BannerService;
use App\Http\Resources\BannerResource;

class BannerController extends Controller
{
    public function __construct(protected BannerService $bannerService)
    {
        
    }
    public function getBannersList(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            return response()->json(
                formated_response(constant: ZONE_ID_REQUIRED_403),
                403
            );
        }

        $zoneIdHeader = $request->header('zoneId');
        $zoneId = $zoneIdHeader === '0' ? null : $zoneIdHeader;
        $isFeatured = $request->boolean('is_featured');
        $limit = $request->integer('limit');
        $offset = $request->integer('offset');

        $getBanners = $this->bannerService->getBanners($isFeatured, $limit, $offset, $zoneId);
        $collection = BannerResource::collection($getBanners);

        return response()->json(formated_response(BANNER_LIST_200, content: $collection, limit: $request->limit, offset: $request->offset));
    }
}
