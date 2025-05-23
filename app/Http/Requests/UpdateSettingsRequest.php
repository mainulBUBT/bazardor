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
            'tab' => ['required', 'string', Rule::in(['general', 'business', 'notifications', 'mail'])],
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
                'enable_multi_language' => 'boolean',
                'enable_geolocation' => 'boolean',
                'company_logo'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'company_favicon'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            'business' => [
                'market_update_frequency' => ['required', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
                'market_verification_process' => ['required', 'string', Rule::in(['admin_approval', 'automatic', 'community_voting'])],
                'market_visibility_range' => 'required|numeric|min:1|max:100',
                'points_for_price_update' => 'required|integer|min:0',
                'points_for_market_update' => 'required|integer|min:0',
                'points_for_new_product' => 'required|integer|min:0',
                'points_for_new_market' => 'required|integer|min:0',
            ],
            'notifications' => [
                'enable_email_notifications' => 'boolean',
                'enable_push_notifications' => 'boolean',
                'enable_sms_notifications' => 'boolean',
                'notify_price_drops' => 'boolean',
                'notify_new_markets' => 'boolean',
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
            default => [],
        });
        dd($rules);
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
        ];
    }
}
