<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZoneStoreUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
            'coordinates' => 'nullable|string',
        ];

        // For update requests, allow unique validation to ignore the current record
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $zoneId = $this->route('zone');
            $rules['name'] = "required|string|max:255|unique:zones,name,{$zoneId}";
        } else {
            $rules['name'] = 'required|string|max:255|unique:zones,name';
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
            'name' => translate('messages.zone_name'),
            'description' => translate('messages.zone_description'),
            'is_active' => translate('messages.active_status'),
            'markets' => translate('messages.markets'),
            'markets.*' => translate('messages.market'),
        ];
    }
} 