<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $productParam = $this->route('product');
        $productId = $productParam ? (is_object($productParam) ? $productParam->id : $productParam) : '';

        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,draft',
            'is_visible' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $productId,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $productId,
            'brand' => 'nullable|string|max:255',
            'country_of_origin' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',

            // Market prices validation
            'market_prices' => 'nullable|array',
            'market_prices.*.market_id' => 'required_with:market_prices.*.price|exists:markets,id',
            'market_prices.*.price' => 'required_with:market_prices.*.market_id|numeric|min:0',
            'market_prices.*.price_date' => 'nullable|date',
        ];

        // Dynamically add validation for any locale suffix in submitted data
        // This handles all current and future locales without relying on cached settings
        $translatableFields = ['name', 'description', 'brand'];
        $defaultLocale = get_default_locale();
        $detectedLocales = [];

        foreach ($translatableFields as $field) {
            foreach (array_keys($this->input()) as $key) {
                if (preg_match('/^' . $field . '_(.+)$/', $key, $matches)) {
                    $detectedLocales[$matches[1]] = true;
                }
            }
        }

        foreach (array_keys($detectedLocales) as $locale) {
            if ($locale === $defaultLocale) {
                continue;
            }
            $rules["name_{$locale}"] = 'nullable|string|max:255';
            $rules["description_{$locale}"] = 'nullable|string';
            $rules["brand_{$locale}"] = 'nullable|string|max:255';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'category_id.required' => 'Please select a category.',
            'unit_id.required' => 'Please select a unit.',
            'status.required' => 'Please select a status.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $marketPrices = $this->input('market_prices', []);
            if (is_array($marketPrices)) {
                $marketIds = array_filter(array_column($marketPrices, 'market_id'));
                if (count($marketIds) !== count(array_unique($marketIds))) {
                    $validator->errors()->add('market_prices', 'Each market can only be selected once.');
                }
            }
        });
    }
} 