<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'amount' => fake()->randomFloat(0, 1), // random floats of 2 decimal place of min 1.00
            'paymentable_id' => fake()->uuid(),
            'paymentable_type' => 'Fake/Payment',
            'gateway' => fake()->randomElement(Payment::GATEWAYS),
            'method' =>  fake()->randomElement(['card', 'transfer', 'ussd']),
            'currency' => fake()->currencyCode(),
            'ip_address' => fake()->ipv4(),
            'transaction_reference' => fake()->uuid(),
            'transaction_status' => $transaction_staus = fake()->randomElement(['pending','failed','successful']),
            'metadata' => null, 
            'paid_at' => $transaction_staus == 'successful'? now(): null,
            'verified_at' => $transaction_staus == 'successful'? now(): null,
            'verifier_id' => fake()->boolean()? User::inRandomOrder()->first()?? User::factory()->create() : null
        ];
    }
}
