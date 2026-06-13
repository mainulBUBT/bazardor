<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FixImages extends Command
{
    protected $signature = 'images:fix
        {type=all : Which entity to process — banners|categories|markets|products|all}
        {--skip-fix : Skip the image_path prefix-cleanup step}';

    protected $description = 'Fix image_path prefixes and download missing Wikipedia/Wikimedia images';

    private const USER_AGENT = 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)';

    /** Directories whose image_path values may carry an accidental "{dir}/" prefix. */
    private const DIRS = [
        'products' => Product::class,
        'markets' => Market::class,
        'categories' => Category::class,
        'banners' => Banner::class,
    ];

    public function handle(): int
    {
        $type = strtolower((string) $this->argument('type'));

        $valid = ['all', 'banners', 'categories', 'markets', 'products'];
        if (! in_array($type, $valid, true)) {
            $this->error("Invalid type '{$type}'. Use one of: ".implode(', ', $valid));

            return self::INVALID;
        }

        if (! $this->option('skip-fix')) {
            $this->fixPaths();
        }

        $run = fn (string $t) => $type === 'all' || $type === $t;

        if ($run('categories')) {
            $this->process('categories', Category::all(), fn ($m) => $this->processCategory($m));
        }
        if ($run('products')) {
            $this->process('products', Product::with('category')->get(), fn ($m) => $this->processProduct($m));
        }
        if ($run('markets')) {
            $this->process('markets', Market::all(), fn ($m) => $this->processMarket($m));
        }
        if ($run('banners')) {
            $this->process('banners', Banner::all(), fn ($m) => $this->processBanner($m));
        }

        $this->newLine();
        $this->info('All done! Images fixed and downloaded.');

        return self::SUCCESS;
    }

    /**
     * Shared driver: filter a collection to rows missing an image, then run a
     * per-entity processor over each with a progress bar and rate-limit sleep.
     */
    private function process(string $dir, $models, callable $processor): void
    {
        $missing = $models->filter(fn (Model $m) => $this->isMissingImage($m, $dir));

        if ($missing->isEmpty()) {
            $this->info("All {$dir} already have image files.");

            return;
        }

        $this->info("Processing {$missing->count()} {$dir}...");
        $bar = $this->output->createProgressBar($missing->count());
        $bar->start();

        foreach ($missing as $model) {
            $processor($model);
            $bar->advance();
            sleep(1); // respect Wikimedia rate limits
        }

        $bar->finish();
        $this->newLine();
    }

    private function isMissingImage(Model $model, string $dir): bool
    {
        if (empty($model->image_path)) {
            return true;
        }

        // Strip any accidental "{dir}/" prefix before checking disk.
        $filename = ltrim(str_replace("{$dir}/", '', $model->image_path), '/');

        // Re-download demo placeholders — they're not real images.
        if (str_starts_with($filename, 'demo-')) {
            return true;
        }

        return ! Storage::disk('public')->exists("{$dir}/{$filename}");
    }

    // ----- per-entity processors -------------------------------------------------

    private const CATEGORY_WIKI = [
        'Grocery' => 'Grocery_store', 'Fish' => 'Fish_market', 'Meat' => 'Meat',
        'Spices' => 'Spice', 'Vegetables' => 'Vegetable_market', 'Fruits' => 'Fruit',
    ];

    private const CATEGORY_COMMONS = [
        'Grocery' => 'grocery store market Bangladesh', 'Fish' => 'fish market Bangladesh',
        'Meat' => 'meat market butcher Bangladesh', 'Spices' => 'spices market Bangladesh',
        'Vegetables' => 'vegetable market Bangladesh', 'Fruits' => 'fruit market Bangladesh',
    ];

    private function processCategory(Category $category): void
    {
        $url = $this->fetchWikipediaImageUrl(self::CATEGORY_WIKI[$category->name] ?? str_replace(' ', '_', $category->name))
            ?? $this->fetchCommonsImageUrl(self::CATEGORY_COMMONS[$category->name] ?? $category->name.' market');

        if ($url && ($saved = $this->downloadAndStore($url, 'categories', $category->slug))) {
            $category->update(['image_path' => $saved]);
            $this->line(" <info>✓</info> {$category->name} — downloaded");

            return;
        }

        $this->line(" <comment>→</comment> {$category->name} — no image found");
    }

    private const PRODUCT_OVERRIDES = [
        'Miniket Rice' => 'Rice', 'Nazirshail Rice' => 'Rice', 'Masoor Dal (Deshi)' => 'Lentil',
        'Soybean Oil (Rupchanda)' => 'Soybean oil', 'Mustard Oil (Radhuni)' => 'Mustard oil',
        'Potato (Diamond)' => 'Potato', 'Onion (Deshi)' => 'Onion', 'Tomato (Ripe)' => 'Tomato',
        'Brinjal (Long)' => 'Eggplant', 'Hilsha Fish (1kg+)' => 'Ilish', 'Rui Fish' => 'Rohu',
        'Beef (Bone-in)' => 'Beef', 'Broiler Chicken' => 'Chicken meat', 'Mango (Himsagar)' => 'Mango',
        'Banana (Sagor)' => 'Banana', 'Turmeric Powder' => 'Turmeric', 'Chili Powder' => 'Chili pepper',
    ];

    private const PRODUCT_FALLBACKS = [
        'vegetable' => 'demo-veg.png', 'fish' => 'demo-fish.png', 'meat' => 'demo-meat.png',
        'fruit' => 'demo-fruit.png', 'grocery' => 'demo-veg.png', 'spice' => 'demo-veg.png',
    ];

    private function processProduct(Product $product): void
    {
        $url = $this->fetchWikipediaImageUrl(self::PRODUCT_OVERRIDES[$product->name] ?? $product->name);

        if ($url && ($saved = $this->downloadAndStore($url, 'products', $product->slug))) {
            $product->update(['image_path' => $saved]);
            $this->line(" <info>✓</info> {$product->name} — downloaded");

            return;
        }

        // Fallback to a category demo image.
        $categoryName = strtolower(optional($product->category)->name ?? '');
        $fallback = 'demo-veg.png';
        foreach (self::PRODUCT_FALLBACKS as $keyword => $filename) {
            if (str_contains($categoryName, $keyword)) {
                $fallback = $filename;
                break;
            }
        }

        $product->update(['image_path' => $fallback]);
        $this->line(" <comment>→</comment> {$product->name} — fallback ({$fallback})");
    }

    private const MARKET_WIKI = [
        'Karwan Bazar' => 'Kawran_Bazar', 'Hatirpool Kacha Bazar' => 'Hatirpool',
        'Mohammadpur Krishi Market' => 'Mohammadpur,_Dhaka', 'Shantinagar Bazar' => 'Shantinagar,_Dhaka',
        'New Market' => 'New_Market,_Dhaka',
    ];

    private const MARKET_COMMONS = [
        'Karwan Bazar' => 'Kawran Bazar Dhaka Bangladesh', 'Hatirpool Kacha Bazar' => 'Hatirpool Dhaka Bangladesh',
        'Mohammadpur Krishi Market' => 'Mohammadpur Dhaka market Bangladesh',
        'Shantinagar Bazar' => 'Shantinagar Dhaka Bangladesh market', 'New Market' => 'New Market Dhaka Bangladesh',
    ];

    private function processMarket(Market $market): void
    {
        $url = $this->fetchWikipediaImageUrl(self::MARKET_WIKI[$market->name] ?? $market->name)
            ?? $this->fetchCommonsImageUrl(self::MARKET_COMMONS[$market->name] ?? $market->name.' Bangladesh market', 600);

        if ($url && ($saved = $this->downloadAndStore($url, 'markets', $market->slug))) {
            $market->update(['image_path' => $saved]);
            $this->line(" <info>✓</info> {$market->name} — downloaded");

            return;
        }

        // Final fallback — demo placeholder.
        $market->update(['image_path' => 'demo-market.png']);
        $this->line(" <comment>→</comment> {$market->name} — fallback (demo-market.png)");
    }

    private const BANNER_WIKI = [
        'Fresh Vegetables' => 'Vegetable_market', 'Eid Special Discount' => 'Eid_al-Fitr',
        'Winter Collection' => 'Winter_vegetables',
    ];

    private const BANNER_COMMONS = [
        'Fresh Vegetables' => 'vegetable market Bangladesh fresh', 'Eid Special Discount' => 'Eid al-Fitr Bangladesh shopping',
        'Winter Collection' => 'winter vegetable Bangladesh market',
    ];

    private function processBanner(Banner $banner): void
    {
        $url = $this->fetchWikipediaImageUrl($this->keywordMatch($banner->title, self::BANNER_WIKI) ?? str_replace(' ', '_', $banner->title))
            ?? $this->fetchCommonsImageUrl($this->keywordMatch($banner->title, self::BANNER_COMMONS) ?? $banner->title.' Bangladesh');

        if ($url && ($saved = $this->downloadAndStore($url, 'banners', Str::slug($banner->title)))) {
            $banner->update(['image_path' => $saved]);
            $this->line(" <info>✓</info> {$banner->title} — downloaded");

            return;
        }

        $this->line(" <comment>→</comment> {$banner->title} — no image found, keeping placeholder");
    }

    /** Return the mapped term for the first keyword contained in $title, or null. */
    private function keywordMatch(string $title, array $map): ?string
    {
        foreach ($map as $keyword => $term) {
            if (str_contains($title, $keyword)) {
                return $term;
            }
        }

        return null;
    }

    // ----- shared HTTP / storage helpers -----------------------------------------

    private function fetchWikipediaImageUrl(string $term): ?string
    {
        try {
            $slug = urlencode(str_replace(' ', '_', $term));
            $response = $this->http()->get("https://en.wikipedia.org/api/rest_v1/page/summary/{$slug}");

            return $response->successful() ? $response->json('thumbnail.source') : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function fetchCommonsImageUrl(string $searchTerm, int $width = 400): ?string
    {
        try {
            $response = $this->http()->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query', 'list' => 'search', 'srsearch' => $searchTerm,
                'srnamespace' => 6, 'srlimit' => 5, 'format' => 'json',
            ]);

            if (! $response->successful()) {
                return null;
            }

            foreach ($response->json('query.search') ?? [] as $result) {
                $title = $result['title'] ?? '';
                if (! preg_match('/\.(jpg|jpeg|png)$/i', $title)) {
                    continue;
                }
                if ($url = $this->getCommonsFileUrl($title, $width)) {
                    return $url;
                }
            }
        } catch (\Throwable) {
        }

        return null;
    }

    private function getCommonsFileUrl(string $fileTitle, int $width = 400): ?string
    {
        try {
            $response = $this->http()->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query', 'titles' => $fileTitle, 'prop' => 'imageinfo',
                'iiprop' => 'url', 'iiurlwidth' => $width, 'format' => 'json',
            ]);

            if (! $response->successful()) {
                return null;
            }

            $pages = $response->json('query.pages') ?? [];
            $page = reset($pages);

            return $page['imageinfo'][0]['thumburl'] ?? $page['imageinfo'][0]['url'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function downloadAndStore(string $url, string $dir, string $slug): ?string
    {
        try {
            $response = $this->http(20)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $filename = Str::slug($slug).'.'.$this->guessExtension($url, $response->header('Content-Type'));
            Storage::disk('public')->put("{$dir}/{$filename}", $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }

    private function guessExtension(string $url, ?string $contentType): string
    {
        if ($contentType && str_contains($contentType, 'jpeg')) {
            return 'jpg';
        }
        if ($contentType && str_contains($contentType, 'png')) {
            return 'png';
        }
        if ($contentType && str_contains($contentType, 'webp')) {
            return 'webp';
        }

        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) ? ($ext === 'jpeg' ? 'jpg' : $ext) : 'jpg';
    }

    private function http(int $timeout = 10): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout($timeout)->withHeaders(['User-Agent' => self::USER_AGENT]);
    }

    private function fixPaths(): void
    {
        $this->info('Fixing image_path prefixes...');

        foreach (self::DIRS as $dir => $model) {
            $count = $model::where('image_path', 'like', $dir.'/%')->update([
                'image_path' => \DB::raw("REPLACE(image_path, '{$dir}/', '')"),
            ]);

            if ($count > 0) {
                $this->line(" ✓ {$dir}: fixed {$count} rows");
            }
        }
    }
}
