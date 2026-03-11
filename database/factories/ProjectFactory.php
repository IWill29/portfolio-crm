<?php

namespace Database\Factories;

use App\Models\Project;
use App\Enums\ProjectStatus;
use App\Enums\ProjectPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
        'title' => fake()->sentence(3),
        'description' => fake()->paragraph(),
        'budget' => fake()->randomFloat(2, 500, 15000), // Budžets no 500 līdz 15k
        'status' => fake()->randomElement(ProjectStatus::cases()), // Paņem nejaušu Enum vērtību
        'priority' => fake()->randomElement(ProjectPriority::cases()), // Paņem nejaušu Enum vērtību
        'starts_at' => fake()->dateTimeBetween('-1 month', 'now'),
        'ends_at' => fake()->dateTimeBetween('now', '+6 months'),
        ];
    }
}
