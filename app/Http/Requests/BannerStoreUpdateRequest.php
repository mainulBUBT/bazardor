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
        $imageRule = ($this->isMethod('post') && $this->type == 'general') ? 'required' : 'nullable';
        return [
            'title' => 'required|string|max:255',
            'image' => [$imageRule, 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'url' => 'required|url|max:255',
            'type' => 'required|in:general,featured',
            'description' => 'required_if:type,general|string',
            'badge_text' => 'required_if:type,featured|string|max:255',
            'badge_color' => 'required_if:type,featured|string|max:50',
            'badge_background_color' => 'required_if:type,featured|string|max:50',
            'badge_icon' => 'required_if:type,featured|string|max:100',
            'button_text' => 'required_if:type,featured|string|max:100',
            'is_active' => 'sometimes|boolean',
            'position' => 'required_if:type,general|integer',
            'start_date' => 'required_if:type,general|date',
            'end_date' => 'required_if:type,general|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => translate('messages.Banner title is required'),
            'title.string' => translate('messages.Banner title must be a string'),
            'title.max' => translate('messages.Banner title is too long'),
            'image.required' => translate('messages.Banner image is required'),
            'image.image' => translate('messages.Banner image must be an image file'),
            'image.mimes' => translate('messages.Banner image must be a jpeg, png, or jpg'),
            'image.max' => translate('messages.Banner image must not exceed 2MB'),
            'url.url' => translate('messages.The link must be a valid URL'),
            'url.max' => translate('messages.The                 link is too long'),
            'type.required' => translate('messages.Banner type is required'),
            'type.in' => translate('messages.Banner type must be general or featured'),
            'description.string' => translate('messages.Description must be a string'),
            'badge_text.string' => translate('messages.Badge text must be a string'),
            'badge_text.max' => translate('messages.Badge text is too long'),
            'badge_color.string' => translate('messages.Badge color must be a string'),
            'badge_color.max' => translate('messages.Badge color is too long'),
            'badge_background_color.string' => translate('messages.Badge background color must be a string'),
            'badge_background_color.max' => translate('messages.Badge background color is too long'),
            'badge_icon.string' => translate('messages.Badge icon must be a string'),
            'badge_icon.max' => translate('messages.Badge icon is too long'),   
            'button_text.string' => translate('messages.Button text must be a string'),
            'button_text.max' => translate('messages.Button text is too long'),
            'is_active.boolean' => translate('messages.Status must be true or false'),
            'position.integer' => 'Position must be an integer',
            'start_date.date' => translate('messages.Start date must be a valid date'),
            'end_date.date' => translate('messages.End date must be a valid date'),
            'end_date.after_or_equal' => translate('messages.End date must be after or equal to start date'),
        ];
    }
}
