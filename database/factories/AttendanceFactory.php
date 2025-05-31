<?php

namespace Database\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random session
        $session = Session::inRandomOrder()->first();

        // Get a random student from the session's group
        $student = $session->Classe->group->students()->inRandomOrder()->first();

        // Random attendance status
        $status = $this->faker->randomElement(['present', 'absent', 'late', 'excused']);

        // Optional random notes
        $notes = $this->faker->boolean() ? $this->faker->sentence : null;

        return [
            'student_id' => $student->id,
            'session_id' => $session->id,
            'status' => $status,
            'notes' => $notes,
        ];
    }
}
