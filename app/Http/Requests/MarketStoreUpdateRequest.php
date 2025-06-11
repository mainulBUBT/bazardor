<?php

namespace App\Http\Requests;

use App\Enums\MarketType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class MarketStoreUpdateRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:markets,slug,' . ($this->market->id ?? ''),
            'type' => ['required', new Enum(MarketType::class)],
            'description' => 'nullable|string',
            'address' => 'required|string|max:500',
            'division' => 'required|string',
            'district' => 'required|string',
            'upazila' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:active,inactive,pending',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opening_hours' => 'nullable|array|size:7',
            'opening_hours.*.opening_time' => 'nullable|date_format:H:i|required_with:opening_hours.*.closing_time',
            'opening_hours.*.closing_time' => 'nullable|date_format:H:i|after_or_equal:opening_hours.*.opening_time|required_with:opening_hours.*.opening_time',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter the market name.',
            'slug.unique' => 'This URL slug is already in use by another market.',
            'type.required' => 'Please select a market type.',
            'address.required' => 'Please enter the market address.',
            'division.required' => 'Please select a division.',
            'district.required' => 'Please select a district.',
            'opening_hours.*.opening_time.date_format' => 'The :attribute must be in a valid HH:MM format.',
            'opening_hours.*.closing_time.date_format' => 'The :attribute must be in a valid HH:MM format.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($days as $day) {
            $attributes["opening_hours.{$day}.opening_time"] = "opening time for {$day}";
            $attributes["opening_hours.{$day}.closing_time"] = "closing time for {$day}";
        }

        return $attributes;
    }
}
