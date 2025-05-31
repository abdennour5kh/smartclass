<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => $this->faker->randomElement([
                'Licence 1ère année System Informatique',
                'Licence 2ère année System Informatique',
                'Licence 3ère année System Informatique',
                'Master 1 Réseaux',
                'Master 2 Réseaux',
            ]),
        ];
    }
}
