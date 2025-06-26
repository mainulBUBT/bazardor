<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ZoneController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "admin", "as" => "admin."], function () {
    Route::group(["prefix" => "auth", "as" => "auth."], function () {
        Route::group(["middleware" => "guest"], function () {
            Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('login', [LoginController::class, 'login'])->name('login.submit');
        });
        Route::group(["middleware" => ["web", "admin"]], function () {
            Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        });
    });
    
    Route::group(["middleware" => []], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Resource Routes
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

        // Settings Routes
        Route::group(["prefix" => "settings", "as" => "settings."], function () {
            Route::get("/", [SettingController::class, 'index'])->name('index');
            Route::post("/update", [SettingController::class, 'updateSettings'])->name('update');
            Route::post('/update-status', [SettingController::class,'updateStatus'])->name('update-status');
        });

        // Users Management Routes
        Route::group(["prefix" => "users", "as" => "users."], function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('create/{role}', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::get('{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::get('show/{user}', [UserManagementController::class, 'show'])->name('show');
            Route::post('update-status/{user}', [UserManagementController::class, 'updateStatus'])->name('update-status');
            Route::get('pending', [UserManagementController::class, 'pending'])->name('pending');
            Route::get('{user}/approve', [UserManagementController::class, 'approve'])->name('approve');
            Route::get('{user}/reject', [UserManagementController::class, 'reject'])->name('reject');
        });

        Route::group(["prefix" => "roles", "as" => "roles."], function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        // Zones
        Route::group(['prefix' => 'zones', 'as' => 'zones.'], function () {
            Route::get('/', [ZoneController::class, 'index'])->name('index');
            Route::get('/create', [ZoneController::class, 'create'])->name('create');
            Route::post('/', [ZoneController::class, 'store'])->name('store');
            Route::get('/{zone}', [ZoneController::class, 'show'])->name('show');
            Route::get('/{zone}/edit', [ZoneController::class, 'edit'])->name('edit');
            Route::put('/{zone}', [ZoneController::class, 'update'])->name('update');
            Route::delete('/{zone}', [ZoneController::class, 'destroy'])->name('destroy');
            Route::post('/{zone}/toggle-status', [ZoneController::class, 'toggleStatus'])->name('toggle-status');
        });
    });
});
