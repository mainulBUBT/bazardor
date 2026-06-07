<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Console\Command;

class FixImages extends Command
{
    protected $signature = 'images:fix';

    protected $description = 'Fix image_path prefixes and download missing images';

    public function handle(): int
    {
        $this->fixPaths();
        $this->call('categories:download-images');
        $this->call('products:download-images');
        $this->call('markets:download-images');
        $this->call('banners:download-images');

        $this->newLine();
        $this->info('All done! Images fixed and downloaded.');

        return self::SUCCESS;
    }

    private function fixPaths(): void
    {
        $fixes = [
            ['model' => Product::class, 'dir' => 'products/', 'label' => 'products'],
            ['model' => Market::class, 'dir' => 'markets/', 'label' => 'markets'],
            ['model' => Category::class, 'dir' => 'categories/', 'label' => 'categories'],
            ['model' => Banner::class, 'dir' => 'banners/', 'label' => 'banners'],
        ];

        $this->info('Fixing image_path prefixes...');

        foreach ($fixes as $fix) {
            $count = $fix['model']::where('image_path', 'like', $fix['dir'] . '%')->update([
                'image_path' => \DB::raw("REPLACE(image_path, '{$fix['dir']}', '')"),
            ]);

            if ($count > 0) {
                $this->line(" ✓ {$fix['label']}: fixed {$count} rows");
            }
        }
    }
}
