<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>  fake()->word().fake()->bothify('???'), // ensure word is more than 3 by attached random 3 letters
            'description' => fake()->realText(),
            'short_description' => fake()->sentence(),
            'weight' => fake()->randomNumber(2),
            'width' => $width = fake()->randomNumber(2),
            'length' => $length = fake()->randomNumber(2),
            'height' => $height = fake()->randomNumber(2),
            'volume' => $length * $width * $height,
            'shelf_life' => fake()->numberBetween(1, 12). " ".fake()->randomElement(['Day(s)', 'Week(s)', 'Month(s)', 'Year(s)']),
            'price' => $price = fake()->randomFloat(0, 1), // random floats of 2 decimal place of min 1.00
            'currency' => 'NGN', //fake()->currencyCode(),
            'regular_price' => $regular_price = $price + fake()->randomFloat(0, 1),
            'discount' => round(($regular_price - $price)/ $regular_price * 100, 2) ,
            'quantity' => fake()->numberBetween(1,100),
            'status' => 'in stock',
            'category_id' => Category::inRandomOrder()->first()?? Category::factory()->create(),
            'store_id' => Store::inRandomOrder()->first()?? Store::factory()->create(),
            'verified_at' => $is_verified = fake()->boolean() ? now(): null,
            'verifier_id' => ($is_verified== true) ? User::inRandomOrder()->first()?? User::factory()->create() : null,
        ];
    }
}
