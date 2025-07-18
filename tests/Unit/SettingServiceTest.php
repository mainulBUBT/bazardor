<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $settingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingService = new SettingService(new Setting());
    }

    /** @test */
    public function it_can_get_notification_settings()
    {
        // Create test notification settings
        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '1'
        ]);

        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'digest_frequency',
            'value' => 'daily'
        ]);

        $settings = $this->settingService->getSettings('notifications');

        $this->assertCount(2, $settings);
        $this->assertEquals('notifications', $settings->first()->settings_type);
    }

    /** @test */
    public function it_can_get_single_notification_setting()
    {
        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '1'
        ]);

        $value = $this->settingService->getSetting('enable_email_notifications', 'notifications');

        $this->assertEquals('1', $value);
    }

    /** @test */
    public function it_returns_null_for_non_existent_setting()
    {
        $value = $this->settingService->getSetting('non_existent_setting', 'notifications');

        $this->assertNull($value);
    }

    /** @test */
    public function it_can_get_setting_with_default_fallback()
    {
        // Test with existing setting
        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '0'
        ]);

        $value = $this->settingService->getSettingWithDefault('enable_email_notifications', 'notifications');
        $this->assertEquals('0', $value);

        // Test with non-existent setting (should return default)
        $defaultValue = $this->settingService->getSettingWithDefault('enable_push_notifications', 'notifications');
        $this->assertTrue($defaultValue); // Default should be true
    }

    /** @test */
    public function it_can_update_notification_settings()
    {
        $data = [
            'enable_email_notifications' => '1',
            'digest_frequency' => 'weekly',
            'quiet_hours_start' => '23:00'
        ];

        $result = $this->settingService->updateSettings($data, 'notifications');

        $this->assertTrue($result);

        // Verify settings were saved (values are JSON encoded)
        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '"1"'
        ]);

        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'digest_frequency',
            'value' => '"weekly"'
        ]);

        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'quiet_hours_start',
            'value' => '"23:00"'
        ]);
    }

    /** @test */
    public function it_can_update_existing_notification_settings()
    {
        // Create existing setting
        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '0'
        ]);

        $data = [
            'enable_email_notifications' => '1',
        ];

        $result = $this->settingService->updateSettings($data, 'notifications');

        $this->assertTrue($result);

        // Verify setting was updated (value is JSON encoded)
        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '"1"'
        ]);

        // Should only have one record
        $this->assertEquals(1, Setting::where('settings_type', 'notifications')
            ->where('key_name', 'enable_email_notifications')
            ->count());
    }

    /** @test */
    public function it_can_update_notification_status()
    {
        $result = $this->settingService->updateStatus('enable_push_notifications', true, 'notifications');

        $this->assertTrue($result);

        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'enable_push_notifications',
            'value' => 'true'
        ]);
    }

    /** @test */
    public function it_can_update_existing_notification_status()
    {
        // Create existing setting
        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'enable_sms_notifications',
            'value' => '1'
        ]);

        $result = $this->settingService->updateStatus('enable_sms_notifications', false, 'notifications');

        $this->assertTrue($result);

        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'enable_sms_notifications',
            'value' => 'false'
        ]);
    }

    /** @test */
    public function it_has_correct_notification_default_settings()
    {
        $defaults = $this->settingService->getDefaultSettings();

        $this->assertArrayHasKey('notifications', $defaults);
        
        $notificationDefaults = $defaults['notifications'];
        
        // Test user notification defaults
        $this->assertTrue($notificationDefaults['enable_email_notifications']);
        $this->assertTrue($notificationDefaults['enable_push_notifications']);
        $this->assertFalse($notificationDefaults['enable_sms_notifications']);
        $this->assertTrue($notificationDefaults['notify_price_drops']);
        $this->assertTrue($notificationDefaults['notify_new_markets']);
        
        // Test admin notification defaults
        $this->assertTrue($notificationDefaults['notify_new_user_registrations']);
        $this->assertTrue($notificationDefaults['notify_new_market_submissions']);
        $this->assertTrue($notificationDefaults['notify_new_product_submissions']);
        $this->assertTrue($notificationDefaults['notify_user_reports_flags']);
        $this->assertTrue($notificationDefaults['notify_system_errors_warnings']);
        
        // Test schedule defaults
        $this->assertEquals('daily', $notificationDefaults['digest_frequency']);
        $this->assertEquals('22:00', $notificationDefaults['quiet_hours_start']);
        $this->assertEquals('07:00', $notificationDefaults['quiet_hours_end']);
        $this->assertEquals('09:00', $notificationDefaults['digest_delivery_time']);
    }

    /** @test */
    public function it_handles_boolean_values_correctly_in_status_update()
    {
        // Test with boolean true
        $this->settingService->updateStatus('test_setting_true', true, 'notifications');
        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'test_setting_true',
            'value' => 'true'
        ]);

        // Test with boolean false
        $this->settingService->updateStatus('test_setting_false', false, 'notifications');
        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'test_setting_false',
            'value' => 'false'
        ]);

        // Test with string '1'
        $this->settingService->updateStatus('test_setting_string_1', '1', 'notifications');
        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'test_setting_string_1',
            'value' => '"1"'
        ]);

        // Test with string '0'
        $this->settingService->updateStatus('test_setting_string_0', '0', 'notifications');
        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'test_setting_string_0',
            'value' => '"0"'
        ]);
    }

    /** @test */
    public function it_only_updates_changed_settings()
    {
        // Create existing settings
        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '1'
        ]);

        Setting::create([
            'settings_type' => 'notifications',
            'key_name' => 'digest_frequency',
            'value' => 'daily'
        ]);

        $data = [
            'enable_email_notifications' => '1', // Same value, shouldn't update
            'digest_frequency' => 'weekly', // Different value, should update
            'quiet_hours_start' => '22:00' // New setting, should create
        ];

        $result = $this->settingService->updateSettings($data, 'notifications');

        $this->assertTrue($result);

        // Verify all settings exist with correct values (JSON encoded)
        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'enable_email_notifications',
            'value' => '"1"'
        ]);

        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'digest_frequency',
            'value' => '"weekly"'
        ]);

        $this->assertDatabaseHas('settings', [
            'settings_type' => 'notifications',
            'key_name' => 'quiet_hours_start',
            'value' => '"22:00"'
        ]);
    }
}