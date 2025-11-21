<?php
use App\Http\Controllers\Api\CategoryMarketController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\ConfigController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('otp-verification', [AuthController::class, 'verifyOtp']);
});

Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::get('profile', [UserManagementController::class, 'profile']);
    Route::post('update-profile', [UserManagementController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::prefix('favorites')->group(function () {
        Route::get('list', [UserManagementController::class, 'listFavorites']);
        Route::post('add', [UserManagementController::class, 'addFavorite']);
        Route::delete('remove', [UserManagementController::class, 'removeFavorite']);
    });

    Route::prefix('products')->group(function () {
        Route::post('create', [UserManagementController::class, 'createProduct']);
        Route::post('submit-price', [UserManagementController::class, 'submitPrice']);
    });
});

// Public API
Route::prefix('categories')->group(function () {
    Route::get('list', [CategoryMarketController::class, 'getCategoriesList']);
    Route::get('get-category', [CategoryMarketController::class, 'getCategory']);
});

Route::prefix('banners')->group(function () {
    Route::get('list', [BannerController::class, 'getBannersList']);
});

Route::prefix('markets')->group(function () {
    Route::get('random-list', [CategoryMarketController::class, 'getRandomMarketsList']);
    Route::get('random-product-list', [CategoryMarketController::class, 'getRandomProductList']);
    Route::get('list', [CategoryMarketController::class, 'getMarketsList']);
});

Route::prefix('config')->group(function () {
    Route::get('/', [ConfigController::class, 'getConfig']);
    Route::get('get-zone', [ConfigController::class, 'getZone']);
});
