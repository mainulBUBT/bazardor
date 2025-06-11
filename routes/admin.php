<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MarketController;

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

    Route::group(["prefix"=> "settings", "as"=> "settings."], function () {
        Route::get("/", [SettingController::class, 'index'])->name('index');
        Route::post("/update", [SettingController::class, 'updateSettings'])->name('update');
        Route::post('/update-status', [SettingController::class,'updateStatus'])->name('update-status');
    });
});
