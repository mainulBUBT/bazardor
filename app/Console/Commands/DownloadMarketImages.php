<?php

namespace App\Console\Commands;

use App\Models\Market;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadMarketImages extends Command
{
    protected $signature = 'markets:download-images';
    protected $description = 'Download Wikipedia/Wikimedia images for markets missing image files';

    // Correct Wikipedia article titles (spellings as they appear on Wikipedia)
    private const WIKIPEDIA_TERMS = [
        'Karwan Bazar'              => 'Kawran_Bazar',
        'Hatirpool Kacha Bazar'     => 'Hatirpool',
        'Mohammadpur Krishi Market' => 'Mohammadpur,_Dhaka',
        'Shantinagar Bazar'         => 'Shantinagar,_Dhaka',
        'New Market'                => 'New_Market,_Dhaka',
    ];

    // Wikimedia Commons search terms as fallback
    private const COMMONS_TERMS = [
        'Karwan Bazar'              => 'Kawran Bazar Dhaka Bangladesh',
        'Hatirpool Kacha Bazar'     => 'Hatirpool Dhaka Bangladesh',
        'Mohammadpur Krishi Market' => 'Mohammadpur Dhaka market Bangladesh',
        'Shantinagar Bazar'         => 'Shantinagar Dhaka Bangladesh market',
        'New Market'                => 'New Market Dhaka Bangladesh',
    ];

    public function handle(): int
    {
        $markets = Market::get()->filter(fn($m) => $this->isMissingImage($m));

        if ($markets->isEmpty()) {
            $this->info('All markets already have image files.');
            return self::SUCCESS;
        }

        $this->info("Processing {$markets->count()} markets...");
        $bar = $this->output->createProgressBar($markets->count());
        $bar->start();

        foreach ($markets as $market) {
            $this->processMarket($market);
            $bar->advance();
            sleep(1); // respect Wikimedia rate limits
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }

    private function isMissingImage(Market $market): bool
    {
        if (empty($market->image_path)) {
            return true;
        }

        $filename = ltrim(str_replace('markets/', '', $market->image_path), '/');

        // Re-download demo placeholders — they're not real market images
        if (str_starts_with($filename, 'demo-')) {
            return true;
        }

        return ! Storage::disk('public')->exists("markets/{$filename}");
    }

    private function processMarket(Market $market): void
    {
        // 1. Try Wikipedia summary thumbnail
        $wikiTerm = self::WIKIPEDIA_TERMS[$market->name] ?? $market->name;
        $imageUrl  = $this->fetchWikipediaImageUrl($wikiTerm);

        // 2. Fall back to Wikimedia Commons search
        if (! $imageUrl) {
            $commonsTerm = self::COMMONS_TERMS[$market->name] ?? $market->name . ' Bangladesh market';
            $imageUrl    = $this->fetchCommonsImageUrl($commonsTerm);
        }

        if ($imageUrl) {
            $saved = $this->downloadAndStore($imageUrl, $market->slug);
            if ($saved) {
                $market->update(['image_path' => $saved]);
                $this->line(" <info>✓</info> {$market->name} — downloaded");
                return;
            }
        }

        // 3. Final fallback — keep existing demo-market.png
        $market->update(['image_path' => 'demo-market.png']);
        $this->line(" <comment>→</comment> {$market->name} — fallback (demo-market.png)");
    }

    private function fetchWikipediaImageUrl(string $term): ?string
    {
        try {
            $slug     = urlencode(str_replace(' ', '_', $term));
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Bazardor/1.0 (mostafiz.6amtech@gmail.com)'])
                ->get("https://en.wikipedia.org/api/rest_v1/page/summary/{$slug}");

            if ($response->successful()) {
                return $response->json('thumbnail.source');
            }
        } catch (\Throwable) {
        }

        return null;
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
                    'srnamespace' => 6,   // File namespace
                    'srlimit'     => 5,
                    'format'      => 'json',
                ]);

            if (! $response->successful()) {
                return null;
            }

            $results = $response->json('query.search') ?? [];

            foreach ($results as $result) {
                $title = $result['title'] ?? '';
                // Only JPG/PNG images
                if (! preg_match('/\.(jpg|jpeg|png)$/i', $title)) {
                    continue;
                }

                $imageUrl = $this->getCommonsFileUrl($title);
                if ($imageUrl) {
                    return $imageUrl;
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
                    'action'  => 'query',
                    'titles'  => $fileTitle,
                    'prop'    => 'imageinfo',
                    'iiprop'  => 'url',
                    'iiurlwidth' => 600,
                    'format'  => 'json',
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

            Storage::disk('public')->put("markets/{$filename}", $response->body());

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
}
