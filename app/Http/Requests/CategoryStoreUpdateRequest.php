<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryStoreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRule = ($this->isMethod('post')) ? 'required' : 'nullable';
        $categoryId = $this->route('category');

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'parent_id' => 'nullable|exists:categories,id',
            'position' => 'nullable|integer',
            'image' => [
                $imageRule,
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
            ],
        ];

        $default = get_default_locale();
        foreach (array_keys($this->input()) as $key) {
            if (preg_match('/^(name|description)_(.+)$/', $key, $m) && $m[2] !== $default) {
                $rules["{$m[1]}_{$m[2]}"] = 'nullable|string|max:' . ($m[1] === 'name' ? '255' : '65535');
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => translate('messages.The category name is required.'),
            'name.unique' => translate('messages.The category name has already been taken.'),
            'slug.unique' => translate('messages.The category slug has already been taken.'),
            'parent_id.exists' => translate('messages.The selected parent category does not exist.'),
            'position.integer' => translate('messages.The position must be an integer.'),
            'image.required' => translate('messages.The category image is required.'),
            'image.image' => translate('messages.The file must be an image (jpeg, png, jpg).'),
            'image.mimes' => translate('messages.The image must be a file of type: jpeg, png, jpg.'),
            'image.max' => translate('messages.The image may not be greater than 2MB.'),
        ];
    }
}
