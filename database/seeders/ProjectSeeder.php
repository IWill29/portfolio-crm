<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Katram klientam izveidojam 1-3 projektus
       Client::all()->each(function ($client) {
        Project::factory()
            ->count(rand(1, 3))
            ->create(['client_id' => $client->id]);
        });
    }
}
