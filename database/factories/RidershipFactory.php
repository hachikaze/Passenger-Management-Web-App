<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PassengerManifest;
use Illuminate\Support\Str;
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
            'origin' => 'Lambingan',
            'destination' => 'Kalawaan',
            //'created_at' => $this->faker->dateTimeBetween('2024-10-11', '2022-12-31'),
            'created_at' => '2024-10-04',
            'updated_at' => now(),
            'is_guest' => 'true',
        ];
    }
}
