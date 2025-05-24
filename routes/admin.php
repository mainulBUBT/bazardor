<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=> "admin", "as"=> "admin."], function () {
    Route::get("/", [DashboardController::class, 'index'])->name('dashboard');

    Route::group(["prefix"=> "settings", "as"=> "settings."], function () {
        Route::get("/", [SettingController::class, 'index'])->name('index');
        Route::post("/update", [SettingController::class, 'updateSettings'])->name('update');
        Route::post('/update-status', [SettingController::class,'updateStatus'])->name('update-status');
    });
});
