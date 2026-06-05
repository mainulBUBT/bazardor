<?php

namespace App\Services;

use App\Models\PriceContribution;
use App\Models\Product;
use App\Traits\SavesTranslations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    use SavesTranslations;

    public function __construct(private Product $product) {}

    /**
     * Get paginated list of products.
     *
     * @param  string|null  $search
     * @param  int|null  $limit
     * @param  int|null  $offset
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProducts($search = null, array $with = [], $limit = null, $offset = null, array $filters = [])
    {
        return $this->product
            ->when(! empty($with), function ($query) use ($with) {
                $query->with($with);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(! empty($filters['category_id']), function ($query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(! empty($filters['sort']), function ($query) use ($filters) {
                match ($filters['sort']) {
                    'name_asc' => $query->orderBy('name', 'asc'),
                    'name_desc' => $query->orderBy('name', 'desc'),
                    default => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate($limit ?? pagination_limit(), ['*'], 'page', $offset ?? 1);
    }

    /**
     * Store a newly created product.
     *
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

            // Generate unique slug from name
            $slug = Str::slug($data['name']).'-'.Str::uuid();

            // Use DB::table to insert directly — bypasses astrotomic translation
            // interception so translatable columns (name, description, brand) are
            // written to the main table alongside the rest of the attributes.
            $productId = (string) Str::uuid();
            DB::table('products')->insert([
                'id' => $productId,
                'name' => $data['name'] ?? '',
                'slug' => $slug,
                'category_id' => $data['category_id'],
                'unit_id' => $data['unit_id'],
                'status' => $data['status'] ?? 'active',
                'is_visible' => $data['is_visible'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'image_path' => $data['image_path'] ?? null,
                'sku' => $data['sku'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'country_of_origin' => $data['country_of_origin'] ?? null,
                'added_by' => $data['added_by'] ?? 'admin',
                'added_by_id' => $data['added_by_id'] ?? null,
                'device_id' => $data['device_id'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $product = $this->product->findOrFail($productId);

            $this->saveTranslations($product, $data, ['name', 'description', 'brand']);

            // Save tags if provided
            if (! empty($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tagText) {
                    if (! $tagText) {
                        continue;
                    }
                    $product->tags()->create(['tag' => $tagText]);
                }
            }

            // Save market prices if provided
            if (! empty($data['market_prices']) && is_array($data['market_prices'])) {
                foreach ($data['market_prices'] as $mp) {
                    if (empty($mp['market_id']) || empty($mp['price'])) {
                        continue;
                    }
                    $product->marketPrices()->create([
                        'market_id' => $mp['market_id'],
                        'price' => $mp['price'],
                        'price_date' => $mp['price_date'] ?? now(),
                        // 'discount_price' => null, // ignored as requested
                    ]);
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
    public function update(array $data, string $id): Product
    {
        return DB::transaction(function () use ($data, $id) {
            $product = $this->findById($id);
            $oldImagePath = $product->image_path;

            if (isset($data['image']) && $data['image']->isValid()) {
                $data['image_path'] = handle_file_upload('products/', $data['image']->getClientOriginalExtension(), $data['image'], $oldImagePath);
            }
            unset($data['image']);

            // Compare against raw column value so locale doesn't affect slug logic.
            $rawName = $product->getRawOriginal('name');
            if (isset($data['name']) && $data['name'] !== $rawName) {
                $slug = Str::slug($data['name']).'-'.Str::uuid();
            } else {
                $slug = $product->slug;
            }

            // Exclude translated attributes (name, description, brand) from update() —
            // astrotomic intercepts setAttribute for them and writes to the current locale's
            // translation instead of the main column. saveTranslations() handles them below.
            $product->update([
                'slug' => $slug,
                'category_id' => $data['category_id'] ?? $product->category_id,
                'unit_id' => $data['unit_id'] ?? $product->unit_id,
                'status' => $data['status'] ?? $product->status,
                'is_visible' => $data['is_visible'] ?? $product->is_visible,
                'is_featured' => $data['is_featured'] ?? $product->is_featured,
                'image_path' => $data['image_path'] ?? $product->image_path,
                'sku' => $data['sku'] ?? $product->sku,
                'barcode' => $data['barcode'] ?? $product->barcode,
                'country_of_origin' => $data['country_of_origin'] ?? $product->country_of_origin,
            ]);

            $this->saveTranslations($product, $data, ['name', 'description', 'brand']);

            // Keep main column in sync with default-locale value for search/sort queries.
            DB::table('products')->where('id', $product->id)->update([
                'name' => $data['name'] ?? $rawName,
                'description' => $data['description'] ?? $product->getRawOriginal('description'),
                'brand' => $data['brand'] ?? $product->getRawOriginal('brand'),
            ]);

            // Update tags (simple replace strategy)
            if (isset($data['tags']) && is_array($data['tags'])) {
                $product->tags()->delete();
                foreach ($data['tags'] as $tagText) {
                    if (! $tagText) {
                        continue;
                    }
                    $product->tags()->create(['tag' => $tagText]);
                }
            }

            // Update or create market prices
            if (isset($data['market_prices']) && is_array($data['market_prices'])) {
                $keepIds = [];
                foreach ($data['market_prices'] as $mp) {
                    if (empty($mp['market_id']) || empty($mp['price'])) {
                        continue;
                    }
                    $priceDate = $mp['price_date'] ?? now();
                    $marketPrice = $product->marketPrices()
                        ->firstOrCreate([
                            'market_id' => $mp['market_id'],
                            'price_date' => $priceDate,
                        ], [
                            'price' => $mp['price'],
                        ]);
                    // If already existed, update price if changed
                    if ($marketPrice->price != $mp['price']) {
                        $marketPrice->update(['price' => $mp['price']]);
                    }
                    $keepIds[] = $marketPrice->id;
                }
                // Remove old prices not in the new list
                $product->marketPrices()->whereNotIn('id', $keepIds)->delete();
            }

            return $product;
        });
    }

    /**
     * Delete product and its image/tags.
     */
    public function delete(string $id): void
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
    public function findById(string $id, array $with = []): Product
    {
        return $this->product->when(! empty($with), function ($query) use ($with) {
            $query->with($with);
        })->findOrFail($id);
    }

    /**
     * Get products available in zone markets with sorting and filtering.
     *
     * @param  int|null  $limit
     * @param  int|null  $offset
     * @param  string  $sort  'random', 'latest', or 'trending'
     * @param  string|null  $categoryId  Optional category filter
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getRandomProductsByZone(
        string $zoneId,
        $limit = null,
        $offset = null,
        string $sort = 'random',
        ?string $categoryId = null,
        ?string $marketId = null
    ) {
        $paginator = $this->product
            ->with([
                'category:id,name,slug,description,image_path,is_active,position',
                'unit:id,name,symbol,unit_type,is_active',
                'marketPrices' => function ($query) use ($zoneId, $marketId) {
                    $query
                        ->select('id', 'product_id', 'market_id', 'price', 'discount_price', 'price_date')
                        ->whereHas('market', function ($marketQuery) use ($zoneId) {
                            $marketQuery
                                ->where('zone_id', $zoneId)
                                ->where('is_active', 1)
                                ->where('visibility', 1);
                        })
                        ->when($marketId, fn ($q) => $q->where('market_id', $marketId))
                        ->with(['market:id,name'])
                        ->orderBy('price_date', 'desc');
                },
            ])
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->whereHas('marketPrices', function ($query) use ($zoneId, $marketId) {
                $query->whereHas('market', function ($marketQuery) use ($zoneId) {
                    $marketQuery
                        ->where('zone_id', $zoneId)
                        ->where('is_active', 1)
                        ->where('visibility', 1);
                })
                ->when($marketId, fn ($q) => $q->where('market_id', $marketId));
            })
            ->active()
            ->visible()
            ->when($sort === 'trending', function ($query) {
                $query->selectSub(
                    PriceContribution::selectRaw('COUNT(*)')
                        ->whereColumn('product_id', 'products.id')
                        ->where('status', 'approved')
                        ->where('created_at', '>=', now()->subDays(7)),
                    'contributions_count'
                )->orderByDesc('contributions_count');
            })
            ->when($sort === 'latest', fn ($q) => $q->latest())
            ->when($sort === 'random', fn ($q) => $q->inRandomOrder())
            ->paginate($limit ?? pagination_limit(), ['*'], 'page', $offset ?? 1);

        $paginator->getCollection()->transform(function ($product) {
            $byMarket = $product->marketPrices->groupBy('market_id');

            // Latest price per market (first entry = most recent due to price_date desc)
            $latestPerMarket = $byMarket->map(fn ($entries) => $entries->first());

            // Zone range — computed from in-memory data, zero extra queries
            $allPrices = $latestPerMarket->map(fn ($mp) => $mp->price);
            $product->setAttribute('zone_price_range', compute_zone_price_range($allPrices));

            // Pick a random market for this product
            $selected = $latestPerMarket->shuffle()->first();

            if ($selected) {
                $history = $byMarket->get($selected->market_id);
                $previous = $history?->get(1); // second entry = previous record for same market

                if ($previous) {
                    $curr = (float) $selected->price;
                    $prev = (float) $previous->price;
                    $trend = $curr > $prev ? 'up' : ($curr < $prev ? 'down' : 'stable');
                    $selected->setAttribute('price_trend', $trend);
                    $selected->setAttribute('previous_price', $prev);
                    $selected->setAttribute('change_amount', round($curr - $prev, 2));
                } else {
                    $selected->setAttribute('price_trend', 'stable');
                    $selected->setAttribute('previous_price', null);
                    $selected->setAttribute('change_amount', null);
                }

                $product->setRelation('marketPrices', collect([$selected]));
            } else {
                $product->setRelation('marketPrices', collect());
            }

            return $product;
        });

        return $paginator;
    }
}
