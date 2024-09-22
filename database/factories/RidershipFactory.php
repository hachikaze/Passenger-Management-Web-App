<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RidershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $profession = ['Student', 'Senior'];

        return [
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'address' => $this->faker->address,
            'contact_number' => $this->faker->phoneNumber,
            'profession' => $profession[array_rand($profession)],
            'age' => $this->faker->numberBetween(18, 80),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'origin' => 'San Joaquin',
            'destination' => 'Kalawaan',
            //'created_at' => $this->faker->dateTimeBetween('2023-01-01', '2023-12-31'),
            'created_at' => Carbon::parse('2024-09-16 10:16:00'),
            'updated_at' => now(),
        ];
    }
}
