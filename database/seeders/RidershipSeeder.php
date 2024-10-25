<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
        // Passenger counts per station for January 7, 2023
        $stations = [
            'Guadalupe' => 350,
            'Hulo' => 90,
            'Valenzuela' => 15,
            'Lambingan' =>  26,
            'Sta.Ana' => 23,
            'PUP' => 0,
            'Quinta' => 118,
            'Lawton' => 31,
            'Escolta' => 177,
            'Maybunga' => 0,
            'San Joaquin' => 3,
            'Kalawaan' => 11,
            'Pinagbuhatan' => 35,
        ];

        $totalStudents = 210;  // Total number of students
        $totalSeniors = 115;    // Total number of seniors
        $totalPassengers = 0; // Total passenger count (all professions)

        // Function to generate a passenger entry
        function generatePassenger($origin, $profession)
        {
            return [
                'first_name' => fake()->firstName(),
                'middle_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'address' => fake()->address(),
                'contact_number' => fake()->phoneNumber(),
                'profession' => $profession,
                'age' => ($profession === 'Student') ? fake()->numberBetween(18, 25) :
                        (($profession === 'Senior') ? fake()->numberBetween(60, 80) : fake()->numberBetween(26, 59)),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'origin' => $origin,
                'destination' => 'Kalawaan',
                'created_at' => '2024-01-31', 
                'updated_at' => now(),
                'is_guest' => 'true',
            ];
        }

        $passengers = [];
        $remainingStudents = $totalStudents;
        $remainingSeniors = $totalSeniors;

        // Generate the passengers for each station
        foreach ($stations as $origin => $count) {
            for ($i = 0; $i < $count; $i++) {
                if ($remainingStudents > 0) {
                    // Assign student if available
                    $passengers[] = generatePassenger($origin, 'Student');
                    $remainingStudents--;
                } elseif ($remainingSeniors > 0) {
                    // Assign senior if available
                    $passengers[] = generatePassenger($origin, 'Senior');
                    $remainingSeniors--;
                } else {
                    // Assign other passengers if no more students/seniors are left
                    $passengers[] = generatePassenger($origin, 'Other');
                }
            }
        }

        // Insert all passengers into the ridership table
        DB::table('ridership')->insert($passengers);
    }
}
