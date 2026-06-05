<?php

namespace App\Http\Requests;

use App\Models\Zone;
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

    protected function prepareForValidation(): void
    {
        $zoneIds = $this->input('zone_ids', []);

        if (is_array($zoneIds) && in_array('all', $zoneIds)) {
            $this->merge([
                'zone_ids' => Zone::pluck('id')->toArray(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $imageRule = $isUpdate ? 'nullable' : 'required';

        $rules = [
            'title' => 'required|string|max:255',
            'image' => [$imageRule, 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'link' => 'nullable|url|max:255',
            'is_active' => 'required|boolean',
            'is_featured' => 'nullable|boolean',
            'zone_ids' => 'nullable|array',
            'zone_ids.*' => 'uuid|exists:zones,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        // Dynamic locale-suffixed title validation (e.g. title_bn)
        $default = get_default_locale();
        foreach (array_keys($this->input()) as $key) {
            if (preg_match('/^title_(.+)$/', $key, $m) && $m[1] !== $default) {
                $rules["title_{$m[1]}"] = 'nullable|string|max:255';
            }
        }

        return $rules;
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
            'link.url' => translate('messages.The link must be a valid URL'),
            'link.max' => translate('messages.The link is too long'),
            'is_active.boolean' => translate('messages.Status must be true or false'),
            'start_date.date' => translate('messages.Start date must be a valid date'),
            'end_date.date' => translate('messages.End date must be a valid date'),
            'end_date.after_or_equal' => translate('messages.End date must be after or equal to start date'),
        ];
    }
}
