<?php

namespace Database\Factories;

use App\Enums\LogisticEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Logistic>
 */
class LogisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $name = fake()->unique()->randomElement(LogisticEnum::values()),
            'email' => $email = fake()->safeEmail(),
            'username' => fake()->userName(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'signature' => fake()->password(),
            'public_key' => fake()->bothify('#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#'),
            'secret_key' => fake()->bothify('#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#*#'),
            'merchant_email' => $email,
            'is_active' => true,
            'is_default' => strtolower($name) =='dhl'? true : false,
        ];
    }
}
