<?php

namespace Database\Factories;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Faker\Factory as FakerFactory;
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
        static $faker = null;
        $faker ??= FakerFactory::create();

        return [
            'title' => $faker->sentence(3),
            'description' => $faker->paragraph(),
            'budget' => $faker->randomFloat(2, 500, 15000), // Budžets no 500 līdz 15k
            'status' => $faker->randomElement(ProjectStatus::cases()), // Paņem nejaušu Enum vērtību
            'priority' => $faker->randomElement(ProjectPriority::cases()), // Paņem nejaušu Enum vērtību
            'starts_at' => $faker->dateTimeBetween('-1 month', 'now'),
            'ends_at' => $faker->dateTimeBetween('now', '+6 months'),
        ];
    }
}
