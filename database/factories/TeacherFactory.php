<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
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
            'user_id' => User::factory()->state(['role' => 'teacher']), 
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // default for testing
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'grade' => $this->faker->randomElement(['Maître Assistant B', 'Maître Assistant A', 'Maître de Conférences A', 'Maître de Conférences B', 'Professeur']),
            'phone_number' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            
        ];
    }
}
