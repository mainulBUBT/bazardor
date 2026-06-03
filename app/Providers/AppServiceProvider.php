<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

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
        $this->configureTranslatable();
        $this->configureMailSettings();
    }

    private function configureTranslatable(): void
    {
        try {
            $enabledLocales = get_enabled_locales();
            $defaultLocale = get_default_locale();

            Config::set('translatable.locales', $enabledLocales);
            Config::set('translatable.fallback_locale', $defaultLocale);
        } catch (\Throwable) {
            // Database not ready yet (migrations, tests, etc.)
        }
    }

    private function configureMailSettings(): void
    {
        try {
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
        } catch (\Throwable) {
            // Database not ready yet (migrations, tests, etc.)
        }
    }
}
