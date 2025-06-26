<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleStoreUpdateRequest extends FormRequest
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
        $rules = [
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        if ($this->isMethod('POST')) {
            // For creating a new role
            $rules['name'] = 'required|string|max:255|unique:roles,name';
        } else {
            // For updating an existing role
            $roleId = $this->route('role')->id;
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => translate('messages.role_name'),
            'permissions' => translate('messages.permissions'),
            'permissions.*' => translate('messages.permission'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => translate('messages.role_name_required'),
            'name.unique' => translate('messages.role_name_already_exists'),
            'permissions.required' => translate('messages.permissions_required'),
            'permissions.*.exists' => translate('messages.invalid_permission'),
        ];
    }
} 