<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadProductImages extends Command
{
    protected $signature = 'products:download-images';
    protected $description = 'Download Wikipedia images for products missing image files';

    // Map category name keywords → existing demo image fallback
    private const CATEGORY_FALLBACKS = [
        'vegetable' => 'demo-veg.png',
        'fish'      => 'demo-fish.png',
        'meat'      => 'demo-meat.png',
        'fruit'     => 'demo-fruit.png',
        'grocery'   => 'demo-veg.png',
        'spice'     => 'demo-veg.png',
    ];

    // Wikipedia search terms for products that need specific overrides
    private const SEARCH_OVERRIDES = [
        'Miniket Rice'         => 'Rice',
        'Nazirshail Rice'      => 'Rice',
        'Masoor Dal (Deshi)'   => 'Lentil',
        'Soybean Oil (Rupchanda)' => 'Soybean oil',
        'Mustard Oil (Radhuni)'   => 'Mustard oil',
        'Potato (Diamond)'     => 'Potato',
        'Onion (Deshi)'        => 'Onion',
        'Tomato (Ripe)'        => 'Tomato',
        'Brinjal (Long)'       => 'Eggplant',
        'Hilsha Fish (1kg+)'   => 'Ilish',
        'Rui Fish'             => 'Rohu',
        'Beef (Bone-in)'       => 'Beef',
        'Broiler Chicken'      => 'Chicken meat',
        'Mango (Himsagar)'     => 'Mango',
        'Banana (Sagor)'       => 'Banana',
        'Turmeric Powder'      => 'Turmeric',
        'Chili Powder'         => 'Chili pepper',
    ];

    public function handle(): int
    {
        $products = Product::with('category')
            ->get()
            ->filter(fn($p) => $this->isMissingImage($p));

        if ($products->isEmpty()) {
            $this->info('All products already have image files.');
            return self::SUCCESS;
        }

        $this->info("Processing {$products->count()} products...");
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $this->processProduct($product);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }

    private function isMissingImage(Product $product): bool
    {
        if (empty($product->image_path)) {
            return true;
        }

        // Strip leading "products/" prefix if present for Storage check
        $filename = ltrim(str_replace('products/', '', $product->image_path), '/');

        // Re-download demo placeholders — they're not real product images
        if (str_starts_with($filename, 'demo-')) {
            return true;
        }

        return ! Storage::disk('public')->exists("products/{$filename}");
    }

    private function processProduct(Product $product): void
    {
        $searchTerm = self::SEARCH_OVERRIDES[$product->name] ?? $product->name;

        $imageUrl = $this->fetchWikipediaImageUrl($searchTerm);

        if ($imageUrl) {
            $saved = $this->downloadAndStore($imageUrl, $product->slug);
            if ($saved) {
                $product->update(['image_path' => $saved]);
                $this->line(" <info>✓</info> {$product->name} — downloaded");
                return;
            }
        }

        // Fallback to category demo image
        $fallback = $this->categoryFallback($product);
        $product->update(['image_path' => $fallback]);
        $this->line(" <comment>→</comment> {$product->name} — fallback ({$fallback})");
    }

    private function fetchWikipediaImageUrl(string $term): ?string
    {
        try {
            $slug = urlencode(str_replace(' ', '_', $term));
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get("https://en.wikipedia.org/api/rest_v1/page/summary/{$slug}");

            if ($response->successful()) {
                return $response->json('thumbnail.source');
            }
        } catch (\Throwable) {
            // Network failure — fall through to demo image
        }

        return null;
    }

    private function downloadAndStore(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $ext      = $this->guessExtension($url, $response->header('Content-Type'));
            $filename = Str::slug($slug) . '.' . $ext;

            Storage::disk('public')->put("products/{$filename}", $response->body());

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

        $path = parse_url($url, PHP_URL_PATH);
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) ? ($ext === 'jpeg' ? 'jpg' : $ext) : 'jpg';
    }

    private function categoryFallback(Product $product): string
    {
        $categoryName = strtolower(optional($product->category)->name ?? '');

        foreach (self::CATEGORY_FALLBACKS as $keyword => $filename) {
            if (str_contains($categoryName, $keyword)) {
                return $filename;
            }
        }

        return 'demo-veg.png';
    }
}
