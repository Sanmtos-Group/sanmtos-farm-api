<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('??-##-??'),
            'discount' => fake()->numberBetween(1, 100),
            'valid_until' => fake()->dateTimeBetween('+1 week', '+5 month')->format('Y-m-d H:i:s'),
            'is_cancelled' => fake()->boolean(),
            'store_id' => Store::inRandomOrder()->first()?? Store::factory()->create(),  
        ];
    }
}
