<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PushNotificationStoreUpdateRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:50',
            'message' => 'required|string|max:150',
            'type' => 'required|in:announcement,price_alert,promotion,system',
            'target_audience' => 'required|in:all,volunteers,inactive,new',
            'zone_id' => 'nullable|exists:zones,id',
            'link_url' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The notification title is required.',
            'title.max' => 'The notification title must not exceed 50 characters.',
            'message.required' => 'The notification message is required.',
            'message.max' => 'The notification message must not exceed 150 characters.',
        ];
    }
}
