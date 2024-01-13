<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CurrencyExchangeRate>
 */
class CurrencyExchangeRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from' => strtoupper(fake()->bothify("???")),
            'to'  => strtoupper(fake()->bothify("???")),
            'value' => fake()->randomFloat(6),
        ];
    }
}