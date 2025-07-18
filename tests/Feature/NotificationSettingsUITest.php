<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationSettingsUITest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'user_type' => 'super_admin',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function notification_settings_page_loads_with_proper_styling()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index', ['tab' => 'notifications']));

        $response->assertStatus(200);
        
        // Check for notification-settings CSS class
        $response->assertSee('notification-settings');
        
        // Check for toggle switches
        $response->assertSee('toggle-switch');
        $response->assertSee('toggle-slider');
        
        // Check for time inputs
        $response->assertSee('type="time"', false);
        
        // Check for JavaScript functions
        $response->assertSee('statusAlert');
        $response->assertSee('validateTimeInputs');
    }

    /** @test */
    public function notification_settings_form_has_proper_validation_classes()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index', ['tab' => 'notifications']));

        $response->assertStatus(200);
        
        // Check for required fields
        $response->assertSee('required');
        
        // Check for form structure that supports validation
        $response->assertSee('form-control');
        
        // Check for form elements that can have validation
        $response->assertSee('select');
        $response->assertSee('input');
    }

    /** @test */
    public function notification_settings_has_responsive_design_elements()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index', ['tab' => 'notifications']));

        $response->assertStatus(200);
        
        // Check for responsive grid classes
        $response->assertSee('col-md-6');
        $response->assertSee('form-row');
        
        // Check for card structure
        $response->assertSee('card shadow mb-4');
        $response->assertSee('card-header');
        $response->assertSee('card-body');
    }

    /** @test */
    public function notification_settings_toggle_switches_have_proper_attributes()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index', ['tab' => 'notifications']));

        $response->assertStatus(200);
        
        // Check for toggle switch attributes
        $response->assertSee('onchange="statusAlert(this)"', false);
        $response->assertSee('data-url');
        
        // Check for specific notification setting names
        $response->assertSee('enable_email_notifications');
        $response->assertSee('enable_push_notifications');
        $response->assertSee('notify_price_drops');
        $response->assertSee('notify_new_markets');
    }

    /** @test */
    public function notification_settings_time_inputs_have_default_values()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index', ['tab' => 'notifications']));

        $response->assertStatus(200);
        
        // Check for default time values
        $response->assertSee('value="22:00"', false); // quiet_hours_start
        $response->assertSee('value="07:00"', false); // quiet_hours_end
        $response->assertSee('value="09:00"', false); // digest_delivery_time
    }

    /** @test */
    public function notification_settings_has_proper_icons()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.settings.index', ['tab' => 'notifications']));

        $response->assertStatus(200);
        
        // Check for FontAwesome icons
        $response->assertSee('fas fa-envelope');
        $response->assertSee('fas fa-mobile-alt');
        $response->assertSee('fas fa-sms');
        $response->assertSee('fas fa-chart-line');
        $response->assertSee('fas fa-store');
        $response->assertSee('fas fa-user-plus');
        $response->assertSee('fas fa-save');
    }
}