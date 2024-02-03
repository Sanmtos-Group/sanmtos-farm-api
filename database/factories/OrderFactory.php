<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        
        $user =  User::inRandomOrder()->first()?? User::factory()->create();
        $address = $user->addresses()->inRandomOrder()->first() ??  $user->addresses()->firstOrCreate(Address::factory()->make([
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name
        ])->toArray());

        $ordered = fake()->boolean();
        $shipped = fake()->boolean();
        $delivered = fake()->boolean();

        return [
            'number' => fake()->unique()->bothify('sf-**#####'),
            'user_id' => $user->id,
            'address_id' => $address,
            'delivery_fee' => $delivery_fee = fake()->randomFloat(0, 10),
            'price' => $price = fake()->randomFloat(0, 1),
            'coupon_id' => null,
            'promo_id' => null,
            'vat' => $vat = (3/100 * $price),
            'total_price' => $delivery_fee + $price + $vat,
            'status' => ($ordered & $shipped & $delivered) ? 'delivered' : (($ordered & $shipped) ? 'shipped' : ($ordered ? 'ordered': 'failed')),
            'ordered_at' => $ordered? fake()->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s') : null,
            'shipped_at' => ($ordered & $shipped) ? fake()->dateTimeBetween('now', '+2 days')->format('Y-m-d H:i:s') : null ,
            'delivered_at' => ($ordered & $shipped & $delivered) ? fake()->dateTimeBetween('+2 days', '+1 week')->format('Y-m-d H:i:s') : null ,
            'failed_at' => $ordered? null : now() ,
            'failure_reason' => $ordered? null : 'payment failed',
        ];
    }
}
