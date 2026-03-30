<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Izveidojam 20 reālistiskus klientus, izmantojot factory
        Client::factory()->count(20)->create();
    }
}
