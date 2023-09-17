<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->imageUrl(640, 480),
            'imageable_id' => fake()->uuid(),
            'imageable_type' => 'Fake/Image'
        ];
    }
}
