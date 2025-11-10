<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        $this->configureMailSettings();
    }

    private function configureMailSettings(): void
    {
        $settingService = app(SettingService::class);
        $mailConfig = $settingService->getSetting('mail_config', MAIL_SETTINGS);

        if (is_array($mailConfig) && !empty($mailConfig['status'])) {
            Config::set('mail', [
                'driver' => $mailConfig['driver'] ?? 'smtp',
                'host' => $mailConfig['host'] ?? '',
                'port' => $mailConfig['port'] ?? '587',
                'username' => $mailConfig['username'] ?? '',
                'password' => $mailConfig['password'] ?? '',
                'encryption' => $mailConfig['encryption'] ?? null,
                'from' => [
                    'address' => $mailConfig['from_address'] ?? 'no-reply@example.com',
                    'name' => $mailConfig['from_name'] ?? config('app.name'),
                ],
                'sendmail' => '/usr/sbin/sendmail -bs',
                'pretend' => false,
            ]);
        }
    }
}
