<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadCategoryImages extends Command
{
    protected $signature = 'categories:download-images';
    protected $description = 'Download relevant Wikipedia/Wikimedia images for categories';

    private const WIKIPEDIA_TERMS = [
        'Grocery'    => 'Grocery_store',
        'Fish'       => 'Fish_market',
        'Meat'       => 'Meat',
        'Spices'     => 'Spice',
        'Vegetables' => 'Vegetable_market',
        'Fruits'     => 'Fruit',
    ];

    private const COMMONS_TERMS = [
        'Grocery'    => 'grocery store market Bangladesh',
        'Fish'       => 'fish market Bangladesh',
        'Meat'       => 'meat market butcher Bangladesh',
        'Spices'     => 'spices market Bangladesh',
        'Vegetables' => 'vegetable market Bangladesh',
        'Fruits'     => 'fruit market Bangladesh',
    ];

    public function handle(): int
    {
        $categories = Category::all()->filter(fn ($c) => $this->isMissingImage($c));

        if ($categories->isEmpty()) {
            $this->info('All categories already have image files.');
            return self::SUCCESS;
        }

        $this->info("Processing {$categories->count()} categories...");
        $bar = $this->output->createProgressBar($categories->count());
        $bar->start();

        foreach ($categories as $category) {
            $this->processCategory($category);
            $bar->advance();
            sleep(1);
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }

    private function isMissingImage(Category $category): bool
    {
        if (empty($category->image_path)) {
            return true;
        }

        // Re-download demo placeholders or old prefixed paths
        if (str_starts_with($category->image_path, 'categories/') || str_starts_with($category->image_path, 'demo-')) {
            return true;
        }

        return ! Storage::disk('public')->exists("categories/{$category->image_path}");
    }

    private function processCategory(Category $category): void
    {
        $wikiTerm = self::WIKIPEDIA_TERMS[$category->name] ?? str_replace(' ', '_', $category->name);
        $imageUrl = $this->fetchWikipediaImageUrl($wikiTerm);

        if (! $imageUrl) {
            $commonsTerm = self::COMMONS_TERMS[$category->name] ?? $category->name . ' market';
            $imageUrl    = $this->fetchCommonsImageUrl($commonsTerm);
        }

        if ($imageUrl) {
            $saved = $this->downloadAndStore($imageUrl, $category->slug);
            if ($saved) {
                $category->update(['image_path' => $saved]);
                $this->line(" <info>✓</info> {$category->name} — downloaded");
                return;
            }
        }

        $this->line(" <comment>→</comment> {$category->name} — no image found");
    }

    private function fetchWikipediaImageUrl(string $term): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get('https://en.wikipedia.org/api/rest_v1/page/summary/' . urlencode($term));

            return $response->successful() ? $response->json('thumbnail.source') : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function fetchCommonsImageUrl(string $searchTerm): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action'      => 'query',
                    'list'        => 'search',
                    'srsearch'    => $searchTerm,
                    'srnamespace' => 6,
                    'srlimit'     => 5,
                    'format'      => 'json',
                ]);

            if (! $response->successful()) {
                return null;
            }

            foreach ($response->json('query.search') ?? [] as $result) {
                $title = $result['title'] ?? '';
                if (! preg_match('/\.(jpg|jpeg|png)$/i', $title)) {
                    continue;
                }
                $url = $this->getCommonsFileUrl($title);
                if ($url) {
                    return $url;
                }
            }
        } catch (\Throwable) {
        }

        return null;
    }

    private function getCommonsFileUrl(string $fileTitle): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action'     => 'query',
                    'titles'     => $fileTitle,
                    'prop'       => 'imageinfo',
                    'iiprop'     => 'url',
                    'iiurlwidth' => 400,
                    'format'     => 'json',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $pages = $response->json('query.pages') ?? [];
            $page  = reset($pages);

            return $page['imageinfo'][0]['thumburl'] ?? $page['imageinfo'][0]['url'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function downloadAndStore(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $ext      = $this->guessExtension($url, $response->header('Content-Type'));
            $filename = Str::slug($slug) . '.' . $ext;

            Storage::disk('public')->put("categories/{$filename}", $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }

    private function guessExtension(string $url, ?string $contentType): string
    {
        if ($contentType && str_contains($contentType, 'jpeg')) return 'jpg';
        if ($contentType && str_contains($contentType, 'png'))  return 'png';
        if ($contentType && str_contains($contentType, 'webp')) return 'webp';

        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) ? ($ext === 'jpeg' ? 'jpg' : $ext) : 'jpg';
    }
}
