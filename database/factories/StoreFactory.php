<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(fake()->numberBetween(2,5), true), 
            // 'slug' => fake()->slug(),
            'email' => fake()->safeEmail(),
            'phone_number' => "0".fake()->randomElement(['7', '8','9']).fake()->randomElement(['0', '1']).fake()->randomNumber(8, true),
            'description' => fake()->realText(),
            'url' => fake()->url(),
            'user_id' => User::factory()->create(), 
        ];
    }
}
