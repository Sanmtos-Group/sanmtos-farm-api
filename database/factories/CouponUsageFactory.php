<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CouponUsage>
 */
class CouponUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'coupon_id' => User::inRandomOrder()->first()?? User::factory()->create(),
            'user_id' => Coupon::inRandomOrder()->first()?? Coupon::factory()->create(),
            'used_at' => now(),
        ];
    }
}
