<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?? User::factory()->create(),
            'stars' => fake()->numberBetween(1, 5),
            'comment' => fake()->realText(),
            'ratingable_id' => fake()->uuid(),
            'ratingable_type' => 'App\Models\Faker',
        ];
    }
}
