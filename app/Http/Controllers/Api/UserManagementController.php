<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct(private UserManagementService $userService)
    {
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

        return response()->json(formated_response(USER_PROFILE_UPDATED_200, UserResource::make($updatedUser)));
    }
}
