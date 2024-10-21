<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Boat;
use App\Models\Setting;
use Carbon\Carbon;

class ResetBoatStatus extends Command
{
    // Command signature
    protected $signature = 'reset:boatstatus';

    // Command description
    protected $description = 'Reset all ACTIVE boats to INACTIVE daily except those under MAINTENANCE';

    public function handle()
    {
        Boat::where('status', 'ACTIVE')
            ->whereNot('status', 'MAINTENANCE')
            ->update(['status' => 'INACTIVE']);

        Setting::updateOrCreate(
            ['key' => 'last_boat_status_update'],
            ['value' => now()->format('Y-m-d')]
        );

        $this->info('Boat statuses have been reset successfully.');
    }
}
