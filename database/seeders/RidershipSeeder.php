<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ridership;

class RidershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ridership::factory()->count(20000)->create();
    }
}