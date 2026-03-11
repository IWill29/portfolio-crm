<?php

use App\Models\Client;
use App\Models\Project;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('full database chain works correctly', function () {
    // 1. Izveidojam klientu
    $client = Client::factory()->create(['name' => 'Testa Klients']);

    // 2. Izveidojam projektu šim klientam
    $project = Project::factory()->create([
        'client_id' => $client->id,
        'title' => 'Testa Projekts'
    ]);

    // 3. Izveidojam dokumentu šim projektam
    $document = Document::factory()->create([
        'project_id' => $project->id,
        'title' => 'Testa Līgums'
    ]);

    // PĀRBAUDES (Assertions)
    
    // Vai klients redz savu projektu?
    expect($client->projects)->toHaveCount(1)
        ->and($client->projects->first()->title)->toBe('Testa Projekts');

    // Vai projekts redz savu dokumentu?
    expect($project->documents)->toHaveCount(1)
        ->and($project->documents->first()->title)->toBe('Testa Līgums');

    // Vai dokuments "zina", kuram klientam tas pieder caur projektu?
    expect($document->project->client->name)->toBe('Testa Klients');
});

test('cascade delete works for client projects', function () {
    $client = Client::factory()->has(Project::factory()->count(3))->create();
    
    expect(Project::count())->toBe(3);

    // Dzēšam klientu
    $client->delete(); // Soft delete - projektiem vēl jābūt
    expect(Project::count())->toBe(3);

    // Dzēšam neatgriezeniski (force delete), lai pārbaudītu cascade
    $client->forceDelete();
    expect(Project::count())->toBe(0);
});
