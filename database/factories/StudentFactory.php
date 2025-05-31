<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'student']),
            'registration_num' => $this->faker->unique()->numberBetween(100000, 999999),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('student123'), 
            'group_id' => Group::factory(), 
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone_number' => $this->faker->phoneNumber(),
            'gender' => 'male',
            'address' => $this->faker->address(),
            'birth_date' => $this->faker->date('Y-m-d', '2005-12-31'),
            'img_url' => '',
        ];
    }
}
