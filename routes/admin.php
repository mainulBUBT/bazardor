<?php

use App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=> "admin", "as"=> "admin."], function () {
    Route::get("/", [Dashboard::class, 'index'])->name('dashboard');

    Route::group(["prefix"=> "settings", "as"=> "settings."], function () {
        Route::get("/", [SettingController::class, 'index'])->name('index');
        Route::post("/update", [SettingController::class, 'updateSettings'])->name('update');
    });
});
