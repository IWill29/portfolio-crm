<?php

namespace Database\Factories;

use App\Models\Client;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
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
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->phoneNumber(),
            'company' => $faker->company(),
            'notes' => $faker->paragraph(),
            'status' => $faker->randomElement(['active', 'inactive', 'lead']),
        ];
    }
}
