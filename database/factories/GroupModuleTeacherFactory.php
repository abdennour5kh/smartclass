<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Module;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classe>
 */
class ClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => Group::inRandomOrder()->first()->id,
            'module_id' => Module::inRandomOrder()->first()->id,
            'teacher_id' => Teacher::inRandomOrder()->first()->id,
        ];
    }
}
