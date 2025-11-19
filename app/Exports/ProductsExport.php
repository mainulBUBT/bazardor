<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Product::with(['category', 'unit', 'tags', 'marketPrices.market', 'priceThreshold'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Category',
            'Unit',
            'Base Price',
            'Tags',
            'Description',
            'Status',
            'Is Visible',
            'Is Featured',
            'Brand',
            'Country of Origin',
            'Min Price',
            'Max Price',
            'Market Prices',
        ];
    }

    public function map($product): array
    {
        // Format market prices as "Market Name: Price; Market Name 2: Price2"
        $marketPrices = $product->marketPrices
            ->groupBy('market_id')
            ->map(function ($prices) {
                // Get the latest price for each market
                $latestPrice = $prices->sortByDesc('price_date')->first();
                return $latestPrice->market->name . ': ' . $latestPrice->price;
            })
            ->implode('; ');

        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->category ? $product->category->name : '',
            $product->unit ? $product->unit->name : '',
            $product->base_price,
            $product->tags->pluck('tag')->implode(', '),
            $product->description,
            $product->status,
            $product->is_visible ? 'Yes' : 'No',
            $product->is_featured ? 'Yes' : 'No',
            $product->brand,
            $product->country_of_origin,
            $product->priceThreshold ? $product->priceThreshold->min_price : '',
            $product->priceThreshold ? $product->priceThreshold->max_price : '',
            $marketPrices,
        ];
    }
}
