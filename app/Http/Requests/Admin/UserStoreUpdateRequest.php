<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use App\Enums\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Models\User;
use Illuminate\Support\Str;

class UserStoreUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : $this->input('id');

        $passwordRules = 'required|string|min:8|confirmed';

        if ($this->isMethod('put') || $this->isMethod('patch') || $this->routeIs('admin.users.update-user')) {
            $passwordRules = 'sometimes|nullable|string|min:8|confirmed';
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($userId)],
            'role' => ['required', new Enum(Role::class)],
            'password' => $passwordRules,
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today', 'after_or_equal:1900-01-01'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'division' => ['nullable', 'string', Rule::in(Location::getDivisions())],
            'email_verified' => ['nullable', 'boolean'],
            'phone_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'subscribed_to_newsletter' => ['nullable', 'boolean'],
        ];
    }
}
