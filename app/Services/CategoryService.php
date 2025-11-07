<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ProductMarketPrice;

class CategoryService
{
    public function __construct(private Category $category)
    {
    }

    /**
     * Summary of getCategories
     * @param string|null $search
     * @param string|null $parentId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCategories($search = null, $parentId = null)
    {
        return $this->category->when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->when($parentId, function ($query) use ($parentId) {
            $query->where('parent_id', $parentId);
        })
        ->latest()->paginate(pagination_limit());
    }

    /**
     * Summary of store
     * @param array $data
     * @return \App\Models\Category
     */
    public function store(array $data): Category
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = handle_file_upload('categories/', $data['image']->getClientOriginalExtension(), $data['image']);
            $data['image_path'] = $imageName;
        }
        unset($data['image']);

        $this->category->name = $data['name'];
        $this->category->slug = $data['slug'];
        $this->category->parent_id = $data['parent_id'] ?? 0;
        $this->category->position = $data['position'] ?? 0;
        $this->category->image_path = $data['image_path'];
        $this->category->is_active = $data['is_active'] ?? 1;
        $this->category->save();
        return $this->category;
    }

    /**
     * Summary of update
     * @param string $categoryId
     * @param array $data
     * @return \App\Models\Category
     */
    public function update(string $categoryId, array $data): Category
    {
        $category = $this->findById($categoryId);
        $oldImagePath = $category->image_path;

        if (isset($data['image']) && $data['image']->isValid()) {
            $imageName = handle_file_upload('categories/', $data['image']->getClientOriginalExtension(), $data['image'],  $oldImagePath);
            $data['image_path'] = $imageName;

            if ($oldImagePath) {
                // Extract filename from old path for deletion
                $oldFilename = basename($oldImagePath);
                handle_file_upload('categories/', '', null, $oldFilename);
            }
        }
        unset($data['image']);
        $category->name = $data['name'] ?? $category->name;
        $category->slug = $data['slug'] ?? $category->slug;
        $category->parent_id = $data['parent_id'] ?? $category->parent_id;
        $category->position = $data['position'] ?? $category->position;
        $category->image_path = $data['image_path'] ?? $category->image_path;
        $category->is_active = $data['is_active'] ?? $category->is_active;
        $category->save();
       
        return $category;
    }

    /**
     * Summary of delete
     * @param string $categoryId
     * @return void
     */
    public function delete(string $categoryId): void
    {
        $category = $this->findById($categoryId);
        if ($category->image_path) {
            // Extract filename from full path for deletion
            $filename = basename($category->image_path);
            handle_file_upload('categories/', '', null, $filename);
        }
        $category->delete();
    }

    /**
     * Summary of status
     * @param string $categoryId
     * @param mixed $status
     * @return void
     */
    public function status(string $categoryId, $status): void
    {
        $category = $this->findById($categoryId);
        $category->is_active = $status;
        $category->save();
    }

    /**
     * Summary of findById
     * @param string $categoryId
     * @return Category
     */
    public function findById(string $categoryId): Category
    {
        return $this->category->findOrFail($categoryId);
    }


    /**
     * Get categories list with unique market and product counts, with optional filters.
     * Uses database-level subqueries for optimal performance.
     */
    public function getCategoriesList(
        ?string $zoneId = null,
        ?int $limit = null,
        ?int $offset = null,
        ?string $search = null,
        ?int $minProducts = null,
        ?int $minMarkets = null
    ) {
        $marketCountSubquery = ProductMarketPrice::query()
            ->join('products', 'products.id', '=', 'product_market_prices.product_id')
            ->join('markets', 'markets.id', '=', 'product_market_prices.market_id')
            ->whereNull('products.deleted_at')
            ->whereNull('markets.deleted_at')
            ->whereColumn('products.category_id', 'categories.id')
            ->when($zoneId, fn($query) => $query->where('markets.zone_id', $zoneId))
            ->selectRaw('COUNT(DISTINCT product_market_prices.market_id)');

        $productCountSubquery = ProductMarketPrice::query()
            ->join('products', 'products.id', '=', 'product_market_prices.product_id')
            ->join('markets', 'markets.id', '=', 'product_market_prices.market_id')
            ->whereNull('products.deleted_at')
            ->whereNull('markets.deleted_at')
            ->whereColumn('products.category_id', 'categories.id')
            ->when($zoneId, fn($query) => $query->where('markets.zone_id', $zoneId))
            ->selectRaw('COUNT(DISTINCT product_market_prices.product_id)');

        $query = $this->category
            ->select('categories.*')
            ->selectSub($marketCountSubquery, 'unique_market_count')
            ->selectSub($productCountSubquery, 'product_count')
            ->when($search, fn($q) => $q->where('categories.name', 'like', "%{$search}%"));

        // Apply min filters using WHERE with subqueries
        if ($minProducts !== null) {
            $query->whereRaw('(SELECT COUNT(DISTINCT product_market_prices.product_id) FROM product_market_prices INNER JOIN products ON products.id = product_market_prices.product_id INNER JOIN markets ON markets.id = product_market_prices.market_id WHERE products.deleted_at IS NULL AND markets.deleted_at IS NULL AND products.category_id = categories.id' . ($zoneId ? ' AND markets.zone_id = ?' : '') . ') >= ?', array_filter([$zoneId, $minProducts]));
        }

        if ($minMarkets !== null) {
            $query->whereRaw('(SELECT COUNT(DISTINCT product_market_prices.market_id) FROM product_market_prices INNER JOIN products ON products.id = product_market_prices.product_id INNER JOIN markets ON markets.id = product_market_prices.market_id WHERE products.deleted_at IS NULL AND markets.deleted_at IS NULL AND products.category_id = categories.id' . ($zoneId ? ' AND markets.zone_id = ?' : '') . ') >= ?', array_filter([$zoneId, $minMarkets]));
        }

        return $query
            ->orderBy('categories.position')
            ->paginate($limit ?? pagination_limit(), ['*'], 'page', $offset ?? 1);
    }
}

