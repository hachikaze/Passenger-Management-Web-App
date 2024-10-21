<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Models\Boat;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Add the reset boat status logic to the schedule
Artisan::command('reset:boatstatus', function () {
    $today = Carbon::today()->toDateString();

    // Check if the reset has already been done today
    $lastUpdateDate = Setting::where('key', 'last_boat_status_update')->value('value');

    if ($lastUpdateDate !== $today) {
        // Reset active boats to inactive (excluding maintenance)
        Boat::where('status', 'ACTIVE')
            ->where('status', '!=', 'MAINTENANCE')
            ->update(['status' => 'INACTIVE']);

        // Store today's date to prevent multiple resets
        Setting::updateOrCreate(
            ['key' => 'last_boat_status_update'],
            ['value' => $today]
        );

        $this->info('Boat statuses reset successfully.');
    } else {
        $this->info('Boat statuses have already been reset today.');
    }
})->purpose('Reset all active boats to inactive daily.');

Route::get('schedule', function (Schedule $schedule) {
    // Schedule the boat status reset task to run daily at midnight
    $schedule->command('reset:boatstatus')->daily();
});