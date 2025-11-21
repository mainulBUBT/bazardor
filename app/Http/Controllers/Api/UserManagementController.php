<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\AddFavoriteRequest;
use App\Http\Requests\ProductStoreUpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\ProductResource;
use App\Services\UserManagementService;
use App\Services\FavoriteService;
use App\Services\ContributionService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct(
        private UserManagementService $userService,
        private FavoriteService $favoriteService,
        private ContributionService $contributionService,
        private ProductService $productService
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

    /**
     * Allow an authenticated user to submit a product price contribution.
     */
    public function submitPrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'market_id' => ['required', 'uuid', 'exists:markets,id'],
            'submitted_price' => ['required', 'numeric', 'min:0.01'],
            'proof_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $result = $this->contributionService->submitPrice(
            user: $request->user(),
            data: $validated,
            proof: $request->file('proof_image')
        );

        if ($result['rate_limited']) {
            return response()->json(
                formated_response(
                    constant: PRICE_SUBMISSION_RATE_LIMITED_429,
                    errors: ['last_submission_at' => $result['last_submission_at']]
                ),
                429
            );
        }

        $contribution = $result['contribution'];

        return response()->json(
            formated_response(
                constant: PRICE_SUBMISSION_CREATED_200,
                content: [
                    'id' => $contribution->id,
                    'submitted_price' => $contribution->submitted_price,
                    'status' => $contribution->status,
                ]
            ),
            200
        );
    }

    /**
     * Allow an authenticated user to create a product (pending admin approval).
     * 
     * @param ProductStoreUpdateRequest $request
     * @return JsonResponse
     */
    public function createProduct(ProductStoreUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Override status and visibility for user-submitted products
        $validated['status'] = 'draft'; // Pending approval
        $validated['is_visible'] = false; // Hidden until approved
        $validated['is_featured'] = false;
        $validated['added_by'] = 'user';
        $validated['added_by_id'] = $request->user()->id;
        
        // Use ProductService to create the product
        $product = $this->productService->store($validated);
        
        // Load relationships for resource
        $product->load(['category', 'unit']);
        
        return response()->json(
            formated_response(
                constant: PRODUCT_SUBMISSION_CREATED_200,
                content: ProductResource::make($product)
            ),
            200
        );
    }
}
