<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct(
        private SettingService $settingService
    ) {
    }

    /**
     * Get business configuration data (non-redundant)
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getConfig(Request $request): JsonResponse
    {
        $generalKeys = [
            'company_name',
            'company_logo',
            'company_address',
            'company_phone',
            'company_email',
            'show_price_comparison',
            'enable_market_ratings',
            'maintenance_mode',
            'android_min_version',
            'android_download_url',
            'ios_min_version',
            'ios_download_url',
            'google_login',
            'facebook_login',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'linkedin_url',
            'youtube_url',
            'timezone',
            'time_format',
            'decimal_places',
            'copyright_text',
            'cookies_text',
        ];

        $generalSettings = $this->settingService->getSettings(keys: $generalKeys)
            ->mapWithKeys(function ($setting) {
                return [$setting->key_name => $setting->value];
            })
            ->toArray();

        $socialLogin = array_map(fn($key) => [
            'login_medium' => str_replace('_login', '', $key),
            'status' => $this->normalizeBoolean(
                ($generalSettings[$key]['enabled'] ?? false)
            ),
        ], ['google_login', 'facebook_login']);

        $configList = [
            'business_name' => $generalSettings['company_name'] ?? null,
            'logo' => $generalSettings['company_logo'] ?? null,
            'address' => $generalSettings['company_address'] ?? null,
            'phone' => $generalSettings['company_phone'] ?? null,
            'email' => $generalSettings['company_email'] ?? null,
            'markets_comparison' => $this->normalizeBoolean($generalSettings['show_price_comparison'] ?? false),
            'enable_market_rating' => $this->normalizeBoolean($generalSettings['enable_market_ratings'] ?? false),
            'maintenance' => $this->normalizeBoolean($generalSettings['maintenance_mode'] ?? false),
            'social_login' => $socialLogin,
            'social_media' => [
                'facebook' => $generalSettings['facebook_url'] ?? null,
                'twitter' => $generalSettings['twitter_url'] ?? null,
                'instagram' => $generalSettings['instagram_url'] ?? null,
                'linkedin' => $generalSettings['linkedin_url'] ?? null,
                'youtube' => $generalSettings['youtube_url'] ?? null,
            ],
            'app_settings' => [
                'android' => [
                    'min_version' => $generalSettings['android_min_version'] ?? null,
                    'download_url' => $generalSettings['android_download_url'] ?? null,
                ],
                'ios' => [
                    'min_version' => $generalSettings['ios_min_version'] ?? null,
                    'download_url' => $generalSettings['ios_download_url'] ?? null,
                ],
            ],
            'timezone' => $generalSettings['timezone'] ?? 'UTC',
            'time_format' => $generalSettings['time_format'] ?? 'H:i',
            'decimal_places' => (int)($generalSettings['decimal_places'] ?? 2),
            'copyright_text' => $generalSettings['copyright_text'] ?? null,
            'cookies_text' => $generalSettings['cookies_text'] ?? null,
        ];

        return response()->json(formated_response(
            CONFIG_200,
            $configList
        ), 200);
    }

    /**
     * Normalize truthy values to boolean
     */
    private function normalizeBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            return $filtered ?? false;
        }

        return !empty($value);
    }
}
