<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ContributionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\PushNotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->as('admin.')->group(function () {
    Route::prefix('auth')->as('auth.')->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
            Route::post('login', [LoginController::class, 'login'])->name('login.submit');
        });
        Route::middleware(['web', 'admin'])->group(function () {
            Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        });
    });
    
    Route::middleware([])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Resource Routes
        Route::get('units/import-export', [UnitController::class, 'importExport'])->name('units.import-export');
        Route::post('units/import', [UnitController::class, 'import'])->name('units.import');
        Route::get('units/export', [UnitController::class, 'export'])->name('units.export');
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
        Route::get('products-bulk/import-export', [ProductController::class, 'bulkImport'])->name('products.bulk-import');
        Route::post('products-bulk/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products-bulk/export', [ProductController::class, 'export'])->name('products.export');

        // Settings Routes
        Route::prefix('settings')->as('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/update', [SettingController::class, 'updateSettings'])->name('update');
            Route::post('/update-status', [SettingController::class,'updateStatus'])->name('update-status');
            Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
            Route::post('/create-backup', [SettingController::class, 'createBackup'])->name('create-backup');
            Route::post('/toggle-maintenance', [SettingController::class, 'toggleMaintenance'])->name('toggle-maintenance');
            Route::post('/social-connect-update', [SettingController::class, 'socialConnectUpdate'])->name('social-connect-update');
            Route::post('/test-mail', [SettingController::class, 'testMail'])->name('test-mail');
        });

        // Contributions
        Route::prefix('contributions')->as('contributions.')->group(function () {
            Route::get('/', [ContributionController::class, 'index'])->name('index');
            Route::post('{contribution}/approve', [ContributionController::class, 'approve'])->name('approve');
            Route::post('{contribution}/reject', [ContributionController::class, 'reject'])->name('reject');
        });

        // API Users Management Routes
        Route::prefix('users')->as('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('create/{userType}', [UserManagementController::class, 'create'])->name('create');
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

        // Admin Management Routes (Spatie role-based)
        Route::prefix('admins')->as('admins.')->middleware([])->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::get('{admin}/edit', [AdminManagementController::class, 'edit'])->name('edit');
            Route::put('{admin}', [AdminManagementController::class, 'update'])->name('update');
            Route::delete('{admin}', [AdminManagementController::class, 'destroy'])->name('destroy');
            Route::get('{admin}', [AdminManagementController::class, 'show'])->name('show');
        });

        Route::prefix('roles')->as('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        // Zones
        Route::prefix('zones')->as('zones.')->group(function () {
            Route::get('/', [ZoneController::class, 'index'])->name('index');
            Route::post('/', [ZoneController::class, 'store'])->name('store');
            Route::get('/{zone}', [ZoneController::class, 'show'])->name('show');
            Route::get('/{zone}/edit', [ZoneController::class, 'edit'])->name('edit');
            Route::put('/{zone}', [ZoneController::class, 'update'])->name('update');
            Route::delete('/{zone}', [ZoneController::class, 'destroy'])->name('destroy');
            Route::post('/{zone}/toggle-status', [ZoneController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Push Notifications
        Route::prefix('push-notifications')->as('push-notifications.')->group(function () {
            Route::get('/', [PushNotificationController::class, 'index'])->name('index');
            Route::get('create', [PushNotificationController::class, 'create'])->name('create');
            Route::post('/', [PushNotificationController::class, 'store'])->name('store');
            Route::get('{pushNotification}', [PushNotificationController::class, 'show'])->name('show');
            Route::get('{pushNotification}/edit', [PushNotificationController::class, 'edit'])->name('edit');
            Route::put('{pushNotification}', [PushNotificationController::class, 'update'])->name('update');
            Route::delete('{pushNotification}', [PushNotificationController::class, 'destroy'])->name('destroy');
            Route::post('{pushNotification}/send', [PushNotificationController::class, 'send'])->name('send');
            Route::post('{pushNotification}/resend', [PushNotificationController::class, 'resend'])->name('resend');
            Route::get('get-estimated-reach', [PushNotificationController::class, 'getEstimatedReach'])->name('get-estimated-reach');
        });

        // Reports
        Route::prefix('reports')->as('reports.')->group(function () {
            Route::get('contributions', [ReportController::class, 'contributions'])->name('contributions');
            Route::get('data-quality', [ReportController::class, 'dataQuality'])->name('data-quality');
            Route::get('markets', [ReportController::class, 'markets'])->name('markets');
            Route::get('prices', [ReportController::class, 'prices'])->name('prices');
        });
    });
});
