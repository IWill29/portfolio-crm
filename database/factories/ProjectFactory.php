<?php

namespace Database\Factories;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Project;
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
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'budget' => $this->faker->randomFloat(2, 500, 15000), // Budžets no 500 līdz 15k
            'status' => $this->faker->randomElement(ProjectStatus::cases()), // Paņem nejaušu Enum vērtību
            'priority' => $this->faker->randomElement(ProjectPriority::cases()), // Paņem nejaušu Enum vērtību
            'starts_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'ends_at' => $this->faker->dateTimeBetween('now', '+6 months'),
        ];
    }
}
