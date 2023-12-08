<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentGateway>
 */
class PaymentGatewayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => count(Payment::GATEWAYS) ? fake()->unique()->randomElement(Payment::GATEWAYS): fake()->unique()->word() ,
            'email' => fake()->safeEmail(),
            'username' => fake()->userName(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'signature' => fake()->password(),
            'public_key' => fake()->bothify('#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#'),
            'secret_key' => fake()->bothify('#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#'),
            'merchant_email' => $email,
            'is_active' => fake()->boolean(),
            'is_default' => fake()->boolean(),
        ];
    }
}
