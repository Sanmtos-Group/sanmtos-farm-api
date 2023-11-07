<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Health & Beauty',
                'Home & Office', 
                'Appliances', 
                'Phone & Tablets', 
                'Electronics',
                'Computing',
                'Fashion',
                'Baby Wears',
                'Gaming',
                'Sporting Goods',
                'Other Categories'
            ]).fake()->unique()->bothify('??-###'),

            'description' => fake()->realText(),
            'slug' => fake()->unique()->slug(),
            'parent_category_id' => null
        ];
    }
    
}
