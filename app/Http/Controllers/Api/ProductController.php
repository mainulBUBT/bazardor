<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Services\ContributionService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private ContributionService $contributionService
    ) {}

    /**
     * List products with sorting and filtering.
     *
     * Sort: random (default), latest, trending.
     * Filters: category_id, market_id.
     */
    public function index(Request $request): JsonResponse
    {
        $zoneId = $request->attributes->get('zoneId');
        $limit = (int) ($request->limit ?? pagination_limit());
        $offset = (int) ($request->offset ?? 1);
        $sort = $request->query('sort', 'random');
        $categoryId = $request->query('category_id');
        $marketId = $request->query('market_id');

        if (! in_array($sort, ['random', 'latest', 'trending'], true)) {
            $sort = 'random';
        }

        $products = $this->productService->getRandomProductsByZone(
            $zoneId, $limit, $offset, $sort, $categoryId, $marketId
        );

        return response()->json(formated_response(
            PRODUCT_200,
            ProductResource::collection($products),
            $limit,
            $offset
        ), 200);
    }

    /**
     * Create a product submitted by a user (pending admin approval).
     */
    public function store(ProductStoreUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Override status and visibility for user-submitted products
        $validated['status'] = 'draft'; // Pending approval
        $validated['is_visible'] = false; // Hidden until approved
        $validated['is_featured'] = false;
        $validated['added_by'] = 'user';
        $validated['added_by_id'] = $request->user()?->id; // null for anonymous users
        $validated['device_id'] = $request->attributes->get('device_id')
                             ?? $request->input('device_id');

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

    /**
     * Submit a product price contribution (supports anonymous users).
     */
    public function submitPrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'market_id' => ['required', 'uuid', 'exists:markets,id'],
            'submitted_price' => ['required', 'numeric', 'min:0.01'],
            'device_id' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user(); // null for anonymous users
        $deviceId = $request->attributes->get('device_id') ?? $validated['device_id'] ?? null;

        $result = $this->contributionService->submitPrice(
            user: $user,
            deviceId: $deviceId,
            data: $validated
        );

        // Rate limiting applies to both authenticated and guest users
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
}
