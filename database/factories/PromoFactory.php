<?php

namespace Database\Factories;

use App\Enums\DiscountTypeEnum;
use App\Models\DiscountType;
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
            'name' => fake()->randomElement(['Black Friday Promo!!!', 'Discount Delight Day Promo!!', 'Loyal Luxuries']),
            'description' => fake()->realText(),
            'discount_type_id' =>  DiscountType::where('code', DiscountTypeEnum::PercentageOff->value)->first()->id ?? null,
            'discount' => fake()->numberBetween(1, 100),
            'requires_min_purchase' => $requires_min_purchase = fake()->boolean(),
            'min_purchase_price' => $requires_min_purchase? fake()->numberBetween(1,10) : 0,
            'is_for_first_purchase_only' => fake()->boolean(),
            'free_delivery' => fake()->boolean(),
            'free_advert' => fake()->boolean(),
            'start_datetime' => ($is_unlimited = fake()->boolean()) ? null :  fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d H:i:s'),
            'end_datetime' => $is_unlimited ? null :  fake()->dateTimeBetween('+3 week', '+2 month')->format('Y-m-d H:i:s'),
            'is_unlimited' => $is_unlimited,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'store_id' => Store::inRandomOrder()->first()?? Store::factory()->create(),
        ];
    }
}
