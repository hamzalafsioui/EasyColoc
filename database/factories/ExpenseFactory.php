<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
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
            'paid_by' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 5, 500),
            'date' => fake()->date(),
            'description' => fake()->sentence(),
        ];
    }
}
