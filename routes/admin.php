<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\BannerController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=> "admin", "as"=> "admin."], function () {
    Route::get("/", [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('units', UnitController::class);
    Route::resource('banners', BannerController::class);
    Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');

    Route::group(["prefix"=> "settings", "as"=> "settings."], function () {
        Route::get("/", [SettingController::class, 'index'])->name('index');
        Route::post("/update", [SettingController::class, 'updateSettings'])->name('update');
        Route::post('/update-status', [SettingController::class,'updateStatus'])->name('update-status');
    });
});
