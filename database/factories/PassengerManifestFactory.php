<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PassengerManifestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profession = ['Student', 'Senior'];
        $gender = ['Male', 'Female'];
        $origin = ['Pinagbuhatan', 'Kalawaan', 'San Joaquin', 'Guadalupe', 'Hulo', 'valenzuela', 'Lambingan', ' Sta-Ana', 'PUP', 'Quinta', 'Lawton', 'Escolta'];
        $destination = ['Pinagbuhatan', 'Kalawaan', 'San Joaquin', 'Guadalupe', 'Hulo', 'valenzuela', 'Lambingan', ' Sta-Ana', 'PUP', 'Quinta', 'Lawton', 'Escolta'];

        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->address(),
            'contact_number' => '09876548253',
            'profession' => $profession[array_rand($profession)],
            'age' => fake()->numberBetween(10, 80),
            'gender' => $gender[array_rand($gender)],
            'origin' => 'Pinagbuhatan',
            //'destination' => $destination[array_rand($destination)],
            'destination' => 'Guadalupe',
            'created_at' => Carbon::parse('2024-09-19 07:23:00'),
        ];
    }
}
