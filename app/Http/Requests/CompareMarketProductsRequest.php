<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareMarketProductsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'market_id_1' => ['required', 'uuid', 'exists:markets,id'],
            'market_id_2' => ['required', 'uuid', 'exists:markets,id', 'different:market_id_1'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'market_id_1.required' => 'First market ID is required.',
            'market_id_1.exists' => 'First market does not exist.',
            'market_id_2.required' => 'Second market ID is required.',
            'market_id_2.exists' => 'Second market does not exist.',
            'market_id_2.different' => 'Please select two different markets to compare.',
            'category_id.exists' => 'Selected category does not exist.',
            'limit.max' => 'Maximum limit is 100 items per page.',
        ];
    }
}
