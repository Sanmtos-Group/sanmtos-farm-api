<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(['Home Address', 'Office Address']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dialing_code' => $this->faker->randomElement(['234']),
            'phone_number' => "0".$this->faker->randomElement(['7', '8','9']).$this->faker->randomElement(['0', '1']).$this->faker->unique()->randomNumber(8, true),
            'address' => fake()->address(),
            'zip_code' => fake()->postcode(),
            'country_id' => Country::inRandomOrder()->first() ?? Country::factory()->create(), 
            'state' => fake()->state(),
            'lga' => fake()->city(),
            'addressable_id' => 'fake/address',
            'addressable_type' => fake()->uuid(),
            'is_preferred' => fake()->boolean(),
        ];
    }
}
