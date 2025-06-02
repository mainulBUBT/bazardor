<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceContributionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We'll handle authentication in the routes
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'market_id' => 'required|exists:markets,id',
            'price' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    // Get the current price for this product/market
                    $currentPrice = \App\Models\ProductMarketPrice::where([
                        'product_id' => $this->product_id,
                        'market_id' => $this->market_id,
                    ])->latest('price_date')->first();

                    if ($currentPrice) {
                        // If new price is more than 50% different from current price, require proof
                        $percentDiff = abs(($value - $currentPrice->price) / $currentPrice->price * 100);
                        if ($percentDiff > 50 && !$this->hasFile('proof_image')) {
                            $fail('Proof image is required for price changes greater than 50%');
                        }
                    }
                },
            ],
            'proof_image' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product',
            'market_id.required' => 'Please select a market',
            'price.required' => 'Please enter the price',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price cannot be negative',
            'proof_image.image' => 'The proof must be an image',
            'proof_image.max' => 'The proof image must not exceed 2MB',
        ];
    }
}
