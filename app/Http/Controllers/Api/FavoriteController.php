<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $favoriteService
    ) {}

    /**
     * List the authenticated user's favorites.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $type = $request->type;
        $limit = $request->limit ?? pagination_limit();
        $offset = $request->offset ?? 1;

        $favorites = $this->favoriteService->list($userId, $type, $limit, $offset);

        return response()->json(formated_response(
            FAVORITE_LIST_200,
            FavoriteResource::collection($favorites),
            $limit,
            $offset
        ), 200);
    }

    /**
     * Add a product or market to favorites.
     */
    public function store(AddFavoriteRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $validated = $request->validated();

        $this->favoriteService->add(
            $userId,
            $validated['type'],
            $validated['favoritable_id']
        );

        return response()->json(
            formated_response(FAVORITE_ADDED_200),
            200
        );
    }

    /**
     * Remove a product or market from favorites.
     */
    public function destroy(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $type = $request->type;
        $favoritableId = $request->favoritable_id;

        if (! $type || ! $favoritableId) {
            return response()->json(formated_response(FAVORITE_INVALID_403), 403);
        }

        $this->favoriteService->remove($userId, $type, $favoritableId);

        return response()->json(formated_response(FAVORITE_REMOVED_200), 200);
    }
}
