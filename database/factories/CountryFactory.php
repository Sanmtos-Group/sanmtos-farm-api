<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>  fake()->unique()->country(),
            'code' => fake()->unique()->countryISOAlpha3(),
            'currency_code' => fake()->currencyCode(),
            'currency_symbol' => fake()->emoji(),
            'language_code' => fake()->languageCode(),
            // 'slug' => fake()->slug(),
        ];
    }
}
