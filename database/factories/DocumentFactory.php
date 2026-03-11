<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      return [
        'title' => fake()->randomElement(['Gala līgums', 'Tehniskā specifikācija', 'Rēķins #'.rand(100, 999), 'Dizaina skice']),
        'type' => fake()->randomElement(['Līgums', 'Rēķins', 'Specifikācija', 'Citi']),
        'notes' => fake()->sentence(),
      ];
    }

}
