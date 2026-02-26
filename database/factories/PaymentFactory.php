<?php

namespace Database\Factories;

use App\Models\Colocation;
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
            'colocation_id' => Colocation::factory(),
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 1, 100),
            'paid_at' => now(),
        ];
    }
}
