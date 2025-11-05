<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\OtpVerificationRequest;
use App\Http\Requests\Api\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Api\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
        // $this->middleware('auth:api')->except(['login', 'register', 'forgotPassword', 'resetPassword', 'sendOtp', 'verifyOtp', 'refresh']);
    }

    /**
     * Summary of register
     * @param \App\Http\Requests\Api\RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $zoneId = $request->header('zoneId');
        $result = $this->authService->register($request->validated(), $zoneId);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'user' => UserResource::make($result['user']),
                'access_token' => $result['access_token'],
            ],
        ], 201);
    }

    /**
     * Summary of login
     * @param \App\Http\Requests\Api\LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $zoneId = $request->header('zoneId');
        $result = $this->authService->login($request->validated(), $zoneId);

        if (!$result['success']) {
            return response()->json(formated_response(constant: $result['message'], content: $result), 401);
        }

        $contents = [
            'user' => UserResource::make($result['user']),
            'access_token' => $result['access_token'],
        ];
        return response()->json(formated_response(constant: $result['message'], content: $contents), 200);
    }

    /**
     * Summary of forgotPassword
     * @param \App\Http\Requests\Api\ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->sendPasswordResetOtp($request->validated());

        if (!$result['success']) {
            return response()->json(formated_response(constant: $result['message'], content: $result), 405);
        }

        return response()->json(formated_response(constant: $result['message'], content: $result), 200);
    }

    /**
     * Summary of resetPassword
     * @param \App\Http\Requests\Api\ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->resetPassword($request->validated());

        if (!$result['success']) {
            return response()->json(formated_response(constant: $result['message'], content: $result), 422);
        }

        return response()->json(formated_response(constant: $result['message'], content: $result), 200);
    }

    /**
     * Summary of verifyOtp
     * @param \App\Http\Requests\Api\OtpVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyOtp(OtpVerificationRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp($request->validated());

        if (!$result['success']) {
            return response()->json(formated_response(constant: $result['message'], content: $result), 403);
        }

        return response()->json(formated_response(constant: $result['message'], content: $result), 200);
    }

    /**
     * Summary of logout
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request->user());

        return response()->json(formated_response(constant: $result['message'], content: $result), 200);
    }

    /**
     * Summary of socialLogin
     * @param \App\Http\Requests\Api\SocialLoginRequest $request
     * @return JsonResponse
     */
    public function socialLogin(SocialLoginRequest $request): JsonResponse
    {
        $zoneId = $request->header('zoneId');
        $result = $this->authService->socialLogin($request->validated(), $zoneId);

        if (!$result['success']) {
            return response()->json(formated_response(constant: $result['message'], content: $result), 401);
        }

        $contents = [
            'user' => UserResource::make($result['user']),
            'access_token' => $result['access_token'],
        ];
        return response()->json(formated_response(constant: $result['message'], content: $contents), 200);
    }
}
