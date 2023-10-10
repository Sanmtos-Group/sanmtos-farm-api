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
            'name' => fake()->words(fake()->numberBetween(1, 3), true),
            'description' => fake()->sentence(),
            'discount' => fake()->numberBetween(1, 100),
            'is_universal' => fake()->boolean(),
            'start_time' => fake()->dateTimeBetween('now', '+1 week'),
            'end_time' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'is_cancel' => fake()->boolean(),
            'store_id' => Store::inRandomOrder()->first()?? Store::factory()->create(),
        ];
    }
}
