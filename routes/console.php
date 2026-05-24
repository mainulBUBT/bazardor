<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily housekeeping — price updates are now immediate (in ContributionService)
Schedule::command('prices:housekeeping')
    ->daily()
    ->withoutOverlapping()
    ->runInBackground();
