<?php

namespace App\Console\Commands;

use App\Services\PriceContributionProcessor;
use Illuminate\Console\Command;

class ProcessPriceContributions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'price-contributions:process';

    /**
     * The console command description.
     */
    protected $description = 'Aggregate pending price contributions, update official prices, and archive processed records';

    public function __construct(private PriceContributionProcessor $processor)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $summary = $this->processor->processPending();

        $this->info('Price contribution processing completed.');
        $this->line('Groups processed: ' . $summary['groups_processed']);
        $this->line('Contributions processed: ' . $summary['contributions_processed']);
        $this->line('Prices updated: ' . $summary['prices_updated']);

        return self::SUCCESS;
    }
}
