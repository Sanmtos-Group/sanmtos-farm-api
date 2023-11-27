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
