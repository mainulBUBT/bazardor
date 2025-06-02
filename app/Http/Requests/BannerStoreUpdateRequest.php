<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerStoreUpdateRequest extends FormRequest
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
        $bannerId = $this->route('banner');
        $imageRule = $this->isMethod('post') ? 'required' : 'nullable';
        return [
            'title' => 'required|string|max:255',
            'image' => [$imageRule, 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'url' => 'nullable|url|max:255',
            'type' => 'required|in:general,featured',
            'is_active' => 'boolean',
            'position' => 'integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Banner title is required',
            'title.string' => 'Banner title must be a string',
            'title.max' => 'Banner title is too long',
            'image.required' => 'Banner image is required',
            'image.image' => 'Banner image must be an image file',
            'image.mimes' => 'Banner image must be a jpeg, png, or jpg',
            'image.max' => 'Banner image must not exceed 2MB',
            'url.url' => 'The link must be a valid URL',
            'url.max' => 'The link is too long',
            'type.required' => 'Banner type is required',
            'type.in' => 'Banner type must be general or featured',
            'is_active.boolean' => 'Status must be true or false',
            'position.integer' => 'Position must be an integer',
            'start_date.date' => 'Start date must be a valid date',
            'end_date.date' => 'End date must be a valid date',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
}
