<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\ProductController;

use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=> "admin", "as"=> "admin."], function () {
    Route::get("/", [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('units', UnitController::class);
    Route::resource('banners', BannerController::class);
    Route::post('banners/status/{banner}', [BannerController::class, 'status'])->name('banners.status');
    
    Route::resource('categories', CategoryController::class);
    Route::post('categories/status/{category}', [CategoryController::class, 'status'])->name('categories.status');

    Route::resource('markets', MarketController::class);
    Route::post('markets/status/{market}', [MarketController::class, 'status'])->name('markets.status');
    Route::get('markets/get-districts/{division}', [MarketController::class, 'getDistricts'])->name('markets.get-districts');
    Route::get('markets/get-thanas/{division}/{district}', [MarketController::class, 'getThanas'])->name('markets.get-thanas');

    Route::resource('products', ProductController::class);

    Route::group(["prefix"=> "settings", "as"=> "settings."], function () {
        Route::get("/", [SettingController::class, 'index'])->name('index');
        Route::post("/update", [SettingController::class, 'updateSettings'])->name('update');
        Route::post('/update-status', [SettingController::class,'updateStatus'])->name('update-status');
    });

    Route::group(["prefix"=> "users", "as"=> "users."], function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('create/{role}', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::get('show/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::post('update-status/{user}', [UserManagementController::class, 'updateStatus'])->name('update-status');
        
        // Pending Users Routes
        Route::get('pending', [UserManagementController::class, 'pending'])->name('pending');
        Route::post('{user}/approve', [UserManagementController::class, 'approve'])->name('approve');
        Route::delete('{user}/reject', [UserManagementController::class, 'reject'])->name('reject');
    });
});
