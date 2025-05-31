<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::inRandomOrder()->first()->id ?? 1, // fallback if empty
            'user_id' => User::factory()->state(['role' => 'admin']), 
            'username' => "jamal",
            'last_name' => "jamal",
            'first_name' => "jamal",
            'phone_number' => 1234567,
            'address' => "Annaba",
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('jamal'), // default for testing
        ];
    }
}
