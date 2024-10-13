<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ridership;
use Illuminate\Support\Facades\DB;

class RidershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stations = [
            'Guadalupe' => 95,
            'Hulo' => 49,
            'Valenzuela' => 0,
            'Lambingan' => 15,
            'Sta.Ana' => 12,
            'PUP' => 3,
            'Quinta' => 40,
            'Lawton' => 15,
            'Escolta' => 67,
            'Maybunga' => 6,
            'San Joaquin' => 23,
            'Kalawaan' => 14,
            'Pinagbuhatan' => 81,
        ];

        $studentCount = 85;
        $seniorCount = 52;

        // Function to generate a passenger
        function generatePassenger($origin, $profession)
        {
            return [
                'first_name' => fake()->firstName(),
                'middle_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'address' => fake()->address(),
                'contact_number' => fake()->phoneNumber(),
                'profession' => $profession,
                'age' => ($profession === 'Student') ? fake()->numberBetween(18, 25) : fake()->numberBetween(60, 80),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'origin' => $origin,
                'destination' => 'Kalawaan',
                'created_at' => '2023-01-05',
                'updated_at' => now(),
                'is_guest' => 'true',
            ];
        }

        $passengers = [];
        $remainingStudents = $studentCount;
        $remainingSeniors = $seniorCount;

        // Iterate through each station and generate the required number of passengers
        foreach ($stations as $origin => $count) {
            for ($i = 0; $i < $count; $i++) {
                // If we still have students to assign, use 'Student'; otherwise, use 'Senior'
                if ($remainingStudents > 0) {
                    $passengers[] = generatePassenger($origin, 'Student');
                    $remainingStudents--;
                } elseif ($remainingSeniors > 0) {
                    $passengers[] = generatePassenger($origin, 'Senior');
                    $remainingSeniors--;
                }
            }
        }

        // Insert the generated passengers into the database
        DB::table('ridership')->insert($passengers);
    }
}