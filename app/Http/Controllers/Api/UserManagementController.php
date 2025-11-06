<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\AddFavoriteRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\FavoriteResource;
use App\Services\UserManagementService;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct(
        private UserManagementService $userService,
        private FavoriteService $favoriteService
    ) {
    }

    /**
     * Summary of profile
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->userService->findById($request->user()->id);

        return response()->json(formated_response(USER_PROFILE_200, UserResource::make($user)));
    }

    /**
     * Summary of updateProfile
     * @param \App\Http\Requests\Api\UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->findById($request->user()->id);
        $validated = $request->validated();

        // Ensure immutable attributes persist
        $payload = array_merge([
            'user_type' => $user->user_type,
            'username' => $user->username,
            'subscribed_to_newsletter' => $validated['subscribed_to_newsletter'] ?? $user->subscribed_to_newsletter,
        ], $validated);

        $updatedUser = $this->userService->update($user->id, $payload);

        return response()->json(formated_response(USER_PROFILE_UPDATED_200, UserResource::make($updatedUser)), 200);
    }

    /**
     * Summary of listFavorites
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function listFavorites(Request $request): JsonResponse
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
     * Summary of addFavorite
     * @param \App\Http\Requests\Api\AddFavoriteRequest $request
     * @return JsonResponse
     */
    public function addFavorite(AddFavoriteRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $validated = $request->validated();

        $favorite = $this->favoriteService->add(
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
     * Summary of removeFavorite
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function removeFavorite(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $type = $request->type;
        $favoritableId = $request->favoritable_id;

        if (!$type || !$favoritableId) {
            return response()->json(formated_response(FAVORITE_INVALID_403), 403);
        }

        $this->favoriteService->remove($userId, $type, $favoritableId);

        return response()->json(formated_response(FAVORITE_REMOVED_200), 200);
    }
}
