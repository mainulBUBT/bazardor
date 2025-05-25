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
        return [
            "name"=> ["required","string",Rule::unique('units','name')->ignore($unitId)],
            "symbol"=> ["required","string",Rule::unique('units','symbol')->ignore($unitId)],
            "unit_type"=> "required|string",
        ];
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
