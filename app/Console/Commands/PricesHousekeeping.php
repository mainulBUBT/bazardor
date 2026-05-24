<?php

namespace App\Console\Commands;

use App\Models\ProductMarketPrice;
use App\Services\SettingService;
use Illuminate\Console\Command;

class PricesHousekeeping extends Command
{
    protected $signature = 'prices:housekeeping';

    protected $description = 'Report outdated prices';

    public function handle(SettingService $settingService): int
    {
        $outdatedDays = (int) $settingService->getSettingWithDefault('outdated_price_days', 'business') ?? 30;

        $outdatedCount = ProductMarketPrice::query()
            ->where('price_date', '<', now()->subDays($outdatedDays))
            ->count();

        $this->info("Outdated prices (no update in {$outdatedDays} days): {$outdatedCount}");
        $this->info('Housekeeping complete.');

        return self::SUCCESS;
    }
}
