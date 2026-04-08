<?php

namespace Database\Factories;

use App\Models\Document;
use Faker\Factory as FakerFactory;
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
        static $faker = null;
        $faker ??= FakerFactory::create();

        return [
            'title' => $faker->randomElement(['Gala līgums', 'Tehniskā specifikācija', 'Rēķins #'.rand(100, 999), 'Dizaina skice']),
            'type' => $faker->randomElement(['Līgums', 'Rēķins', 'Specifikācija', 'Citi']),
            'notes' => $faker->sentence(),
        ];
    }
}
