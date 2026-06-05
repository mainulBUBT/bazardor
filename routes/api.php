<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\MarketController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group by default.
|
*/

// Auth
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('otp-verification', [AuthController::class, 'verifyOtp']);
});

// Authenticated user
Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('update-profile', [ProfileController::class, 'update']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('add', [FavoriteController::class, 'store']);
        Route::delete('remove', [FavoriteController::class, 'destroy']);
    });
});

// Public (guest-track: supports both authenticated and anonymous)
Route::middleware('guest-track')->prefix('products')->group(function () {
    Route::post('create', [ProductController::class, 'store']);
    Route::post('submit-price', [ProductController::class, 'submitPrice']);
});

// Market comparisons (no zone required — must be registered before the {id} wildcard)
Route::prefix('markets')->group(function () {
    Route::get('compare', [MarketController::class, 'compare']);
    Route::get('compare-products', [MarketController::class, 'compareProducts']);
});

// Public (zone required)
Route::middleware('zone')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('{id}', [CategoryController::class, 'show']);
    });

    Route::prefix('markets')->group(function () {
        Route::get('random', [MarketController::class, 'random']);
        Route::get('/', [MarketController::class, 'index']);
        Route::get('{id}', [MarketController::class, 'show']);
        Route::get('{id}/products', [MarketController::class, 'products']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
    });

    Route::prefix('banners')->group(function () {
        Route::get('/', [BannerController::class, 'index']);
    });

    Route::prefix('stats')->group(function () {
        Route::get('pulse', [StatsController::class, 'pulse']);
    });
});

// Config & Zones
Route::prefix('config')->group(function () {
    Route::get('/', [ConfigController::class, 'getConfig']);
    Route::get('get-zone', [ConfigController::class, 'getZone']);
});

// Units
Route::get('units', [UnitController::class, 'index']);
