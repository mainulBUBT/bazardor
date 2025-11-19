<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Market;
use App\Models\ProductTag;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->processRow($row);
        }
    }

    private function processRow($row)
    {
        // Skip empty rows
        if (!isset($row['name']) || empty($row['name'])) {
            return;
        }

        $id = $row['id'] ?? null;
        $product = null;

        if ($id) {
            $product = Product::find($id);
        }

        // Find category by name
        $categoryName = $row['category'];
        $category = Category::where('name', 'LIKE', $categoryName)->first();

        if (!$category) {
            // Skip if category not found to avoid bad data
            return; 
        }

        // Find unit by name
        $unitName = $row['unit'];
        $unit = Unit::where('name', 'LIKE', $unitName)->first();
        
        if (!$unit) {
            return;
        }

        $data = [
            'name' => $row['name'],
            'sku' => $row['sku'],
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'base_price' => $row['base_price'] ?? 0,
            'description' => $row['description'],
            'status' => $row['status'] ?? 'active',
            'is_visible' => $this->parseBoolean($row['is_visible']),
            'is_featured' => $this->parseBoolean($row['is_featured']),
            'brand' => $row['brand'],
            'country_of_origin' => $row['country_of_origin'],
        ];

        if ($product) {
            $product->update($data);
        } else {
            $product = Product::create($data);
        }

        // Handle Tags
        if (isset($row['tags'])) {
            $tags = array_filter(array_map('trim', explode(',', $row['tags'])));
            $product->tags()->delete(); // Clear existing tags
            foreach ($tags as $tagText) {
                if (!$tagText) continue;
                $product->tags()->create(['tag' => $tagText]);
            }
        }

        // Handle Price Threshold
        if (isset($row['min_price']) || isset($row['max_price'])) {
            $product->priceThreshold()->updateOrCreate([], [
                'min_price' => $row['min_price'] ?? null,
                'max_price' => $row['max_price'] ?? null,
            ]);
        }

        // Handle Market Prices
        // Format: "Market Name: Price; Market Name 2: Price2"
        if (isset($row['market_prices']) && !empty($row['market_prices'])) {
            $marketPricesData = [];
            $entries = explode(';', $row['market_prices']);
            
            foreach ($entries as $entry) {
                $entry = trim($entry);
                if (empty($entry)) continue;
                
                // Parse "Market Name: Price"
                $parts = explode(':', $entry);
                if (count($parts) !== 2) continue;
                
                $marketName = trim($parts[0]);
                $price = trim($parts[1]);
                
                // Find market by name
                $market = Market::where('name', 'LIKE', $marketName)->first();
                if (!$market) continue;
                
                // Update or create market price
                $product->marketPrices()->updateOrCreate(
                    [
                        'market_id' => $market->id,
                    ],
                    [
                        'price' => $price,
                        'price_date' => now(),
                    ]
                );
            }
        }
    }

    private function parseBoolean($value)
    {
        if (is_bool($value)) return $value;
        $value = strtolower((string)$value);
        return in_array($value, ['yes', 'true', '1', 'on']);
    }
}
