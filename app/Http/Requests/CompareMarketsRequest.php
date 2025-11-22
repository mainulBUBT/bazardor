<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareMarketsRequest extends FormRequest
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
            'user_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'user_lng' => ['nullable', 'numeric', 'between:-180,180'],
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
            'user_lat.between' => 'Latitude must be between -90 and 90.',
            'user_lng.between' => 'Longitude must be between -180 and 180.',
        ];
    }
}
