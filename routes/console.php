<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Shiprocket order status sync ──────────────────────────────────────────────
// Runs every 5 minutes. Fetches live status from Shiprocket for all active
// orders (those not yet in a terminal state: reached / delivered / cancelled).
Schedule::command('shiprocket:sync-status')
    ->everyFiveMinutes()
    ->withoutOverlapping()       // skip if a previous run is still in progress
    ->runInBackground()          // don't block other scheduled tasks
    ->appendOutputTo(storage_path('logs/shiprocket-sync.log'));

