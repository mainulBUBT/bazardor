<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingService
{
    public function __construct(private Setting $setting)
    {

    }

    /**
     * Summary of getSettings
     * @param mixed $group
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Setting>
     */
    public function getSettings(?string $group = null, array $keys = [])
    {
        return $this->setting
            ->when(!is_null($group), function ($query) use ($group) {
                $query->where('settings_type', $group);
            })
            ->when(!empty($keys), function ($query) use ($keys) {
                $query->whereIn('key_name', $keys);
            })
            ->get();
    }


    /**
     * Summary of getSetting
     * @param string $key
     * @param string $group
     */
    public function getSetting(string $key, ?string $group = null): mixed
    {
        return $this->setting
            ->when(!is_null($group), function ($query) use ($group) {
                return $query->where('settings_type', $group);
            })
            ->where('key_name', $key)
            ->value('value');
    }
    
    /**
     * Get a single setting value by key with default fallback
     *
     * @param string $key The setting key
     * @param string $group The settings group/type
     * @return mixed The setting value or default value if not found
     */
    public function getSettingWithDefault(string $key, string $group)
    {
        $value = $this->getSetting($key, $group);
        
        if ($value !== null) {
            return $value;
        }
        
        $defaults = $this->getDefaultSettings();
        return $defaults[$group][$key] ?? null;
    }
    
    /**
     * Summary of updateSettings
     * @param array $data
     * @param string $group
     * @return bool
     */
    public function updateSettings(array $data, string $group): bool
    {
        // Handle file uploads
        if (isset($data['company_logo']) && $data['company_logo']->isValid()) {
            $oldLogo = $this->getSetting('company_logo', $group);
            $data['company_logo'] = handle_file_upload(
                'company/', 
                $data['company_logo']->getClientOriginalExtension(),
                $data['company_logo'],
                $oldLogo
            );
        }   
        
        if (isset($data['company_favicon']) && $data['company_favicon']->isValid()) {
            $oldFavicon = $this->getSetting('company_favicon', $group);
            $data['company_favicon'] = handle_file_upload(
                'company/', 
                $data['company_favicon']->getClientOriginalExtension(),
                $data['company_favicon'],
                $oldFavicon
            );
        }

        if ($group == MAIL_SETTINGS) {
            $data = $this->formatMailConfig($data);
        }

        try {
            DB::transaction(function () use ($data, $group) {
                    $existing = $this->setting->where('settings_type', $group)->pluck('value', 'key_name');
                    foreach ($data as $key => $value) {
                        if (!$existing->has($key) || $existing[$key] != $value) {
                            $this->setting->updateOrCreate(
                                ['settings_type' => $group, 'key_name' => $key],
                                ['value' => $value]
                            );
                        }
                    }
            });

            return true;
        } catch (\Exception $e) {
            info("Settings update failed: " . $e->getMessage());
            return false;
        }   
    }

    /**
     * Summary of updateStatus
     * @param mixed $id
     * @param mixed $status
     * @param mixed $group
     * @return bool
     */
    public function updateStatus($id, $status, $group)
    {
        $setting = $this->setting->where('settings_type', $group)
                                ->where('key_name', $id)
                                ->first();

        if ($setting) {
            $setting->update(['value' => $status]);
        } else {
            $this->setting->create([
                'settings_type' => $group,
                'key_name' => $id,
                'value' => $status
            ]);
        }
        
        return true;
    }

    private function formatMailConfig(array $data): array
    {
        return [
            'mail_config' => [
                'status' => array_key_exists('status', $data) ? (bool)$data['status'] : true,
                'driver' => $data['driver'] ?? 'smtp',
                'host' => $data['host'] ?? '',
                'port' => $data['port'] ?? '587',
                'username' => $data['username'] ?? '',
                'password' => $data['password'] ?? '',
                'encryption' => $data['encryption'] ?? 'tls',
                'from_address' => $data['from_address'] ?? ($data['email'] ?? ''),
                'from_name' => $data['from_name'] ?? ($data['name'] ?? config('app.name')),
            ],
        ];
    }

    /**
     * Get default settings
     *
     * @return array
     */
    public function getDefaultSettings(): array
    {
        return [
            'general' => [
                'company_name' => 'Bazar-dor',
                'company_email' => 'contact@bazar-dor.com',
                'company_phone' => '+880 1234-567890',
                'company_address' => '123 Market Street',
                'auto_approve_users' => true,
                'auto_approve_markets' => false,
                'auto_approve_products' => false,
                'show_price_comparison' => true,
                'enable_price_trend_indicators' => true,
                'enable_market_ratings' => true,
                'enable_volunteer_points_system' => true,
                'facebook_url' => 'https://facebook.com/bazardor',
                'twitter_url' => 'https://twitter.com/bazardor',
                'instagram_url' => 'https://instagram.com/bazardor',
                'linkedin_url' => 'https://linkedin.com/company/bazardor',
                'youtube_url' => 'https://youtube.com/bazardor',
            ],
            'appearance' => [
                'primary_color' => '#4e73df',
                'secondary_color' => '#1cc88a',
                'theme_mode' => 'light',
                'font_family' => 'Nunito',
            ],
            'business' => [
                'market_update_frequency' => 'daily',
                'market_update_cutoff_time' => '17:00',
                'product_update_frequency' => 'daily',
                'product_update_cutoff_time' => '17:00',
                'timezone' => 'UTC',
                'time_format' => 'H:i',
                'decimal_places' => 2,
                'copyright_text' => '© 2025 Bazar-dor. All rights reserved.',
                'cookies_text' => 'We use cookies to enhance your experience.',
            ],
            'notifications' => [
                'enable_email_notifications' => true,
                'enable_push_notifications' => true,
                'notify_system_errors_warnings' => true,
                'firebase_service_file' => '',
                'firebase_api_key' => '',
                'firebase_project_id' => '',
                'firebase_storage_bucket' => '',
                'firebase_auth_domain' => '',
                'firebase_measurement_id' => '',
                'firebase_messaging_sender_id' => '',
                'firebase_app_id' => '',
                'firebase_sender_id' => '',
            ],
            'mail' => [
                'driver' => 'smtp',
                'host' => 'smtp.mailtrap.io',
                'port' => '2525',
                'encryption' => 'tls',
                'username' => '',
                'password' => '',
                'from_address' => 'no-reply@bazar-dor.com',
                'from_name' => 'Bazar-dor',
            ],
            SOCIAL_SETTINGS => [
                'google_client_id' => '',
                'google_client_secret' => '',
                'facebook_client_id' => '',
                'facebook_client_secret' => '',
                'enable_google_login' => true,
                'enable_facebook_login' => true,
            ],
            'security' => [
                'enable_two_factor_auth' => true,
                'min_password_length' => 8,
                'password_expiration_days' => 90,
                'session_timeout_minutes' => 30,
            ],
            APP_SETTINGS => [
                'android_min_version' => '1.0.0',
                'android_download_url' => 'https://play.google.com/store/apps/details?id=com.example.app',
                'ios_min_version' => '1.0.0',
                'ios_download_url' => 'https://apps.apple.com/us/app/example-app/id123456789',
            ],
            'backup' => [
                'backup_frequency' => 'daily',
                'backup_retention_days' => 30,
                'backup_location' => 'local',
                'include_database' => true,
                'include_uploads' => true,
                'encrypt_backups' => true,
            ],
        ];
    }
}
