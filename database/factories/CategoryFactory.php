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
<<<<<<< HEAD
            'name'=>$this->faker->name(),
            'slug'=>$this->faker->slug(),
            'parent_category_id'=>$this->faker->unique(),
            'description'=>$this->faker->text()
=======
            'name' => fake()->unique()->word(), 
            'description' => fake()->realText(),
            'slug' => fake()->unique()->slug(),
            'parent_category_id' => null
>>>>>>> 501db72082300fc37744af6d0b1af26809249f5b
        ];
    }
}
