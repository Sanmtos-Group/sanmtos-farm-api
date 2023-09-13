<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'name' => fake()->unique()->word().'-'.fake()->unique()->word(),
           'description' => fake()->realText(),
           'store_id' => fake()->boolean()? Store::inRandomOrder()->first()?? Store::factory()->create() : null,
           'creator_id' => fake()->boolean()? User::inRandomOrder()->first()?? User::factory()->create() : null,
        ];
    }
}
