<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tab = $this->query('tab', 'general');
        $rules = [
            'tab' => ['required', 'string', Rule::in(['general', 'business', 'notifications', 'mail', 'social', 'security', 'backup', 'app'])],
        ];
        
        $rules = array_merge($rules, match($tab) {
            'general' => [
                'company_name' => 'required|string|max:255',
                'company_email' => 'required|email|max:255',
                'company_phone' => 'required|string|max:20',
                'company_address' => 'required|string|max:500',
                'auto_approve_users' => 'boolean',
                'auto_approve_markets' => 'boolean',
                'auto_approve_products' => 'boolean',
                'show_price_comparison' => 'boolean',
                'enable_price_trend_indicators' => 'boolean',
                'enable_market_ratings' => 'boolean',
                'enable_volunteer_points_system' => 'boolean',
                'company_logo'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_favicon'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'facebook_url' => 'nullable|url|max:500',
                'twitter_url' => 'nullable|url|max:500',
                'instagram_url' => 'nullable|url|max:500',
                'linkedin_url' => 'nullable|url|max:500',
                'youtube_url' => 'nullable|url|max:500',
            ],
            'business' => [
                'market_update_frequency' => ['required', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
                'market_update_cutoff_time' => ['required', 'date_format:H:i'],
                'product_update_frequency' => ['required', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
                'product_update_cutoff_time' => ['required', 'date_format:H:i'],
                'timezone' => 'required|string|max:50',
                'time_format' => ['required', 'string', Rule::in(['H:i', 'h:i A'])],
                'decimal_places' => 'required|integer|min:0|max:10',
                'copyright_text' => 'nullable|string|max:500',
                'cookies_text' => 'nullable|string|max:500',
            ],
            'notifications' => [
                'enable_email_notifications' => 'boolean',
                'enable_push_notifications' => 'boolean',
                'notify_system_errors_warnings' => 'boolean',
                'firebase_service_file' => 'nullable|string',
                'firebase_api_key' => 'nullable|string|max:255',
                'firebase_project_id' => 'nullable|string|max:255',
                'firebase_storage_bucket' => 'nullable|string|max:255',
                'firebase_auth_domain' => 'nullable|string|max:255',
                'firebase_measurement_id' => 'nullable|string|max:255',
                'firebase_messaging_sender_id' => 'nullable|string|max:255',
                'firebase_app_id' => 'nullable|string|max:255',
                'firebase_sender_id' => 'nullable|string|max:255',
            ],
            'mail' => [
                'driver' => ['required', 'string', Rule::in(['smtp', 'sendmail', 'mailgun', 'ses', 'postmark', 'log'])],
                'host' => 'required_if:driver,smtp|string|max:255',
                'port' => 'required_if:driver,smtp|string|max:10',
                'encryption' => 'nullable|string|in:tls,ssl',
                'username' => 'nullable|string|max:255',
                'password' => 'nullable|string|max:255',
                'from_address' => 'required|email|max:255',
                'from_name' => 'required|string|max:255',
            ],
            'social' => [
                'google_client_id' => 'nullable|string|max:255',
                'google_client_secret' => 'nullable|string|max:255',
                'facebook_client_id' => 'nullable|string|max:255',
                'facebook_client_secret' => 'nullable|string|max:255',
                'enable_google_login' => 'boolean',
                'enable_facebook_login' => 'boolean',
            ],
            'app' => [
                'android_min_version' => 'required|string|max:20',
                'android_download_url' => 'required|url|max:500',
                'ios_min_version' => 'required|string|max:20',
                'ios_download_url' => 'required|url|max:500',
            ],
            default => [],
        });
  
        return $rules;
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'boolean' => 'The :attribute field must be true or false.',
            'integer' => 'The :attribute must be an integer.',
            'min' => 'The :attribute must be at least :min.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'in' => 'The selected :attribute is invalid.',
            'regex' => 'The :attribute format is invalid. It should be a valid hex color code (e.g., #1a2b3c).',
            'timezone' => 'The :attribute must be a valid timezone.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'date_format' => 'The :attribute must be a valid time in HH:MM format.',
            'digest_frequency.in' => 'The digest frequency must be real-time, daily, or weekly.',
            'quiet_hours_start.date_format' => 'The quiet hours start time must be in HH:MM format.',
            'quiet_hours_end.date_format' => 'The quiet hours end time must be in HH:MM format.',
            'digest_delivery_time.date_format' => 'The digest delivery time must be in HH:MM format.',
            'android_min_version.required' => 'The Android minimum version is required.',
            'android_download_url.required' => 'The Android download URL is required.',
            'android_download_url.url' => 'The Android download URL must be a valid URL.',
            'ios_min_version.required' => 'The iOS minimum version is required.',
            'ios_download_url.required' => 'The iOS download URL is required.',
            'ios_download_url.url' => 'The iOS download URL must be a valid URL.',
        ];
    }
}
