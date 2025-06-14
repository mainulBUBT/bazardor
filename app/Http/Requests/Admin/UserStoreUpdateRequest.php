<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserStoreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : $this->input('id');

        $passwordRules = 'required|string|min:8|confirmed';
        // Make password optional on update
        if ($this->isMethod('put') || $this->isMethod('patch') || $this->routeIs('admin.users.update-user')) {
            $passwordRules = 'sometimes|nullable|string|min:8|confirmed';
        }

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($userId)],
            'role' => ['required', new Enum(Role::class)],
            'password' => $passwordRules,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
