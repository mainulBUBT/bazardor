<?php

namespace App\Console\Commands;

use App\Models\Banner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadBannerImages extends Command
{
    protected $signature = 'banners:download-images';
    protected $description = 'Download relevant Wikipedia/Wikimedia images for banners missing image files';

    // Wikipedia article titles matched to banner title keywords
    private const WIKIPEDIA_TERMS = [
        'Fresh Vegetables'    => 'Vegetable_market',
        'Eid Special Discount' => 'Eid_al-Fitr',
        'Winter Collection'   => 'Winter_vegetables',
    ];

    // Wikimedia Commons search terms as fallback
    private const COMMONS_TERMS = [
        'Fresh Vegetables'    => 'vegetable market Bangladesh fresh',
        'Eid Special Discount' => 'Eid al-Fitr Bangladesh shopping',
        'Winter Collection'   => 'winter vegetable Bangladesh market',
    ];

    public function handle(): int
    {
        $banners = Banner::all()->filter(fn ($b) => $this->isMissingImage($b));

        if ($banners->isEmpty()) {
            $this->info('All banners already have image files.');
            return self::SUCCESS;
        }

        $this->info("Processing {$banners->count()} banners...");
        $bar = $this->output->createProgressBar($banners->count());
        $bar->start();

        foreach ($banners as $banner) {
            $this->processBanner($banner);
            $bar->advance();
            sleep(1); // respect Wikimedia rate limits
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }

    private function isMissingImage(Banner $banner): bool
    {
        if (empty($banner->image_path)) {
            return true;
        }

        // Re-download if still using a demo placeholder
        if (str_starts_with($banner->image_path, 'banners/demo-') || str_starts_with($banner->image_path, 'demo-')) {
            return true;
        }

        // Strip any accidental 'banners/' prefix before checking disk
        $filename = ltrim(str_replace('banners/', '', $banner->image_path), '/');

        return ! Storage::disk('public')->exists("banners/{$filename}");
    }

    private function processBanner(Banner $banner): void
    {
        $wikiTerm = $this->resolveWikiTerm($banner->title);
        $imageUrl = $this->fetchWikipediaImageUrl($wikiTerm);

        if (! $imageUrl) {
            $commonsTerm = $this->resolveCommonsTerm($banner->title);
            $imageUrl    = $this->fetchCommonsImageUrl($commonsTerm);
        }

        if ($imageUrl) {
            $saved = $this->downloadAndStore($imageUrl, Str::slug($banner->title));
            if ($saved) {
                $banner->update(['image_path' => $saved]);
                $this->line(" <info>✓</info> {$banner->title} — downloaded");
                return;
            }
        }

        // Keep existing file if present (strip wrong prefix and re-save correctly)
        $this->line(" <comment>→</comment> {$banner->title} — no image found, keeping placeholder");
    }

    private function resolveWikiTerm(string $title): string
    {
        foreach (self::WIKIPEDIA_TERMS as $keyword => $term) {
            if (str_contains($title, $keyword)) {
                return $term;
            }
        }
        return str_replace(' ', '_', $title);
    }

    private function resolveCommonsTerm(string $title): string
    {
        foreach (self::COMMONS_TERMS as $keyword => $term) {
            if (str_contains($title, $keyword)) {
                return $term;
            }
        }
        return $title . ' Bangladesh';
    }

    private function fetchWikipediaImageUrl(string $term): ?string
    {
        try {
            $slug     = urlencode($term);
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get("https://en.wikipedia.org/api/rest_v1/page/summary/{$slug}");

            if (! $response->successful()) {
                return null;
            }

            return $response->json('thumbnail.source');
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

    private function getCommonsFileUrl(string $fileTitle, int $width = 400): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get('https://commons.wikimedia.org/w/api.php', [
                    'action'     => 'query',
                    'titles'     => $fileTitle,
                    'prop'       => 'imageinfo',
                    'iiprop'     => 'url',
                    'iiurlwidth' => $width,
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
            $filename = $slug . '.' . $ext;

            Storage::disk('public')->put("banners/{$filename}", $response->body());

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
