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
        return [
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
                'max:2048', // 2MB max size
            ],
        ];
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
