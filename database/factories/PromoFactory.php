<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promo>
 */
class PromoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('???-####'),
            'name' => fake()->words(fake()->numberBetween(1, 3), true),
            'description' => fake()->sentence(),
            'discount' => fake()->numberBetween(1, 100),
            'start_datetime' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d H:i:s'),
            'end_datetime' => fake()->dateTimeBetween('+1 week', '+2 month')->format('Y-m-d H:i:s'),
            'is_cancelled' => fake()->boolean(),
            'store_id' => Store::inRandomOrder()->first()?? Store::factory()->create(),                      
        ];
    }
}
