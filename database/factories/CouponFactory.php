<?php

namespace Database\Factories;

use App\Enums\DiscountTypeEnum;
use App\Models\DiscountType;
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
            'description' => fake()->realText(),
            'discount_type_id' =>  DiscountType::where('code', DiscountTypeEnum::PercentageOff->value)->first()->id ?? null,
            'discount' => fake()->numberBetween(1, 100),
            'requires_min_purchase' => $requires_min_purchase = fake()->boolean(),
            'min_purchase_price' => $requires_min_purchase? fake()->numberBetween(1,10) : 0,
            'is_for_first_purchase_only' => fake()->boolean(),
            'max_usage' => true,
            'unlimited_usage' => false,
            'expiration_date' =>now()->addDays(fake()->numberBetween(7, 60)),
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'store_id' => Store::inRandomOrder()->first()?? Store::factory()->create(),
        ];
    }
}
 