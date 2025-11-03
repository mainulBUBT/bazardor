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
     * Initialize default settings if they do not exist
     *
     * @return void
     */
    public function getSettings(?string $group = null)
    {   
        $settings = $this->setting->where('settings_type', $group)->get();
        return $settings;
    }
    
    /**
     * Get a single setting value by key
     *
     * @param string $key The setting key
     * @param string $group The settings group/type
     * @return string|null The setting value or null if not found
     */
    public function getSetting(string $key, string $group): ?string
    {
        return $this->setting
            ->where('settings_type', $group)
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
            info(["Settings update failed: " =>  $e->getMessage()]);
            return false;
        }   
    }

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
