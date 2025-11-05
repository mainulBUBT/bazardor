<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'dob' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:1900-01-01'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'city' => ['required', 'string', 'max:255'],
            'division' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users'],
            'referred_by' => ['nullable', 'string', 'exists:users,referral_code'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email address is already registered',
            'phone.unique' => 'This phone number is already registered',
            'dob.required' => 'Date of birth is required',
            'dob.before_or_equal' => 'Date of birth cannot be in the future',
            'dob.after_or_equal' => 'Date of birth must be after January 1, 1900',
            'city.required' => 'City is required',
            'division.required' => 'Division is required',
            'gender.required' => 'Gender is required',
            'gender.in' => 'Gender must be one of: male, female, or other',
            'password.required' => 'Password is required',
            'password.confirmed' => 'Password confirmation does not match',
            'image.image' => 'Profile image must be an image file',
            'image.mimes' => 'Profile image must be a jpeg, png, jpg, or gif file',
            'image.max' => 'Profile image must not exceed 2MB',
        ];
    }
}
