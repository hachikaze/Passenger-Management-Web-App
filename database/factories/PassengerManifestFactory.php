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
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'address' => $this->faker->address,
            'contact_number' => $this->faker->phoneNumber,
            'profession' => $profession[array_rand($profession)],
            'age' => $this->faker->numberBetween(18, 80),
            'gender' => $gender[array_rand($gender)],
            'origin' => $destination[array_rand($origin)],
            'destination' => $destination[array_rand($destination)],
            'created_at' => Carbon::parse('2024-09-19 07:23:00'),
        ];
    }
}
