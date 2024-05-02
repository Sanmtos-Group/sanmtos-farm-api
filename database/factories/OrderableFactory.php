<?php

namespace Database\Factories;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orderable>
 */
class OrderableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'orderable_id' => $product = Product::inRandomOrder()->first()?? Product::factory()->create(),
            'orderable_type' => Product::class,
            'quantity' => $quantity =  random_int(1,10),
            'price' =>  $product->price,
            'total_price' => $quantity * $product->price ,
        ];
    }
}
