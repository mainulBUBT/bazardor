<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductTag;
use App\Models\EntityCreator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(private Product $product)
    {
    }

    /**
     * Get paginated list of products.
     *
     * @param string|null $search
     * @param array $with
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProducts($search = null, array $with = [])
    {
        return $this->product
            ->when(!empty($with), function ($query) use ($with) {
                $query->with($with);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(pagination_limit());
    }

    /**
     * Store a newly created product.
     *
     * @param array $data
     * @return Product
     * @throws \Throwable
     */
    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Handle image upload if present
            if (isset($data['image']) && $data['image']->isValid()) {
                $data['image_path'] = handle_file_upload('products/', $data['image']->getClientOriginalExtension(), $data['image']);
            }
            unset($data['image']);

            $product = $this->product->create([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'unit_id' => $data['unit_id'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
                'is_visible' => $data['is_visible'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'image_path' => $data['image_path'] ?? null,
                'sku' => $data['sku'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'brand' => $data['brand'] ?? null,
                'base_price' => $data['base_price'] ?? 0,
            ]);

            // Save tags if provided
            if (!empty($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tagText) {
                    if (!$tagText) continue;
                    $product->tags()->create(['tag' => $tagText]);
                }
            }

            // // Record creator information
            // if (auth()->check()) {
            //     EntityCreator::create([
            //         'user_id' => auth()->id(),
            //         'creatable_id' => $product->id,
            //         'creatable_type' => Product::class,
            //     ]);
            // }

            return $product;
        });
    }

    /**
     * Update an existing product.
     */
    public function update(array $data, int $id): Product
    {
        return DB::transaction(function () use ($data, $id) {
            $product = $this->findById($id);
            $oldImagePath = $product->image_path;

            if (isset($data['image']) && $data['image']->isValid()) {
                $data['image_path'] = handle_file_upload('products/', $data['image']->getClientOriginalExtension(), $data['image'], $oldImagePath);
            }
            unset($data['image']);

            $product->fill([
                'name' => $data['name'] ?? $product->name,
                'category_id' => $data['category_id'] ?? $product->category_id,
                'unit_id' => $data['unit_id'] ?? $product->unit_id,
                'description' => $data['description'] ?? $product->description,
                'status' => $data['status'] ?? $product->status,
                'is_visible' => $data['is_visible'] ?? $product->is_visible,
                'is_featured' => $data['is_featured'] ?? $product->is_featured,
                'image_path' => $data['image_path'] ?? $product->image_path,
                'sku' => $data['sku'] ?? $product->sku,
                'barcode' => $data['barcode'] ?? $product->barcode,
                'brand' => $data['brand'] ?? $product->brand,
                'base_price' => $data['base_price'] ?? $product->base_price,
            ]);
            $product->save();

            // Update tags (simple replace strategy)
            if (isset($data['tags']) && is_array($data['tags'])) {
                $product->tags()->delete();
                foreach ($data['tags'] as $tagText) {
                    if (!$tagText) continue;
                    $product->tags()->create(['tag' => $tagText]);
                }
            }

            return $product;
        });
    }

    /**
     * Delete product and its image/tags.
     */
    public function delete(int $id): void
    {
        $product = $this->findById($id);
        if ($product->image_path) {
            $filename = basename($product->image_path);
            handle_file_upload('products/', '', null, $filename);
        }
        $product->tags()->delete();
        $product->delete();
    }

    /**
     * Find product by id
     */
    public function findById(int $id, array $with = []) : Product
    {
        return $this->product->when(!empty($with), function($query) use ($with){
            $query->with($with);
        })->findOrFail($id);
    }
} 