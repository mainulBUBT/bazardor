<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Services\BannerService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(protected BannerService $bannerService) {}

    public function index(Request $request)
    {
        $zoneIdHeader = $request->attributes->get('zoneId');
        $zoneId = $zoneIdHeader === '0' ? null : $zoneIdHeader;
        $isFeatured = $request->boolean('is_featured');
        $limit = $request->integer('limit');
        $offset = $request->integer('offset');

        $getBanners = $this->bannerService->getBanners($isFeatured, $limit, $offset, $zoneId);
        $collection = BannerResource::collection($getBanners);

        return response()->json(formated_response(BANNER_LIST_200, content: $collection, limit: $request->limit, offset: $request->offset));
    }
}
