<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first(),
            // رتب المستخدمين ال10 ترتيب عشوائي وخدلي اول واحد 
            'title' => fake()->sentence,
            'descreption' => fake()->paragraph,
            // ولد جملة او مقالة
            'periority' => fake()->randomElement(['High', 'Medium', 'Low']),
        ];
    }
}
