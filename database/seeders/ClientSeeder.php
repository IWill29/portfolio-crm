<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Izveidojam 20 reālistiskus klientus, izmantojot factory
       \App\Models\Client::factory()->count(20)->create();
    }

}
