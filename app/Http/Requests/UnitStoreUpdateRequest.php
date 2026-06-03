<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UnitStoreUpdateRequest extends FormRequest
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
        $unitId = $this->route('unit');
        $rules = [
            "name"=> ["required","string",Rule::unique('units','name')->ignore($unitId)],
            "symbol"=> ["required","string",Rule::unique('units','symbol')->ignore($unitId)],
            "unit_type"=> "required|string",
        ];

        // Dynamically add validation for any locale suffix in submitted data
        $translatableFields = ['name', 'symbol'];
        $defaultLocale = get_default_locale();
        $detectedLocales = [];

        foreach ($translatableFields as $field) {
            foreach (array_keys($this->input()) as $key) {
                if (preg_match('/^' . $field . '_(.+)$/', $key, $matches)) {
                    $detectedLocales[$matches[1]] = true;
                }
            }
        }

        foreach (array_keys($detectedLocales) as $locale) {
            if ($locale === $defaultLocale) continue;
            $rules["name_{$locale}"] = 'nullable|string|max:255';
            $rules["symbol_{$locale}"] = 'nullable|string|max:50';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            "name.required"=> "Unit name is required",
            "name.string"=> "Unit name must be a string",
            "name.unique"=> "Unit name already exists",
            "symbol.required"=> "Unit symbol is required",
            "symbol.string"=> "Unit symbol must be a string",
            "symbol.unique"=> "Unit symbol already exists",
            "unit_type.required"=> "Unit type is required",
            "unit_type.string"=> "Unit type must be a string",
        ];
    }
}
