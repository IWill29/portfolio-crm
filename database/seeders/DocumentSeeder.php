<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Katram projektam pievienojam 1-2 dokumentu ierakstus
       Project::all()->each(function ($project) {
         Document::factory()
            ->count(rand(1, 2))
            ->create(['project_id' => $project->id]);
       });
    }
}
