<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\semester>
 */
class SemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'promotion_id' => Promotion::factory(),
            'name' => $this->faker->randomElement([
                'Semestre 1', 'Semestre 2', 'Semestre 3', 'Semestre 4',
                'Semestre 5', 'Semestre 6'
            ]),
        ];
    }
}
