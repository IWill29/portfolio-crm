<?php

use App\Filament\Resources\ProjectResource;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Actions\DeleteAction;
use function Pest\Livewire\livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->actingAs($this->admin);
});

test('can render project list page', function () {
    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertSuccessful();
});

test('can list projects', function () {
    $client = Client::factory()->create();

    $projects = Project::factory()
        ->for($client)
        ->count(3)
        ->create();

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertCanSeeTableRecords($projects)
        ->assertCanRenderTableColumn('title')
        ->assertCanRenderTableColumn('client.name');
});

test('can search projects by title', function () {
    $client = Client::factory()->create();

    $project = Project::factory()
        ->for($client)
        ->create(['title' => 'Unique Project Title']);

    Project::factory()
        ->for($client)
        ->create(['title' => 'Other Project']);

    livewire(ProjectResource\Pages\ListProjects::class)
        ->searchTable('Unique Project Title')
        ->assertCanSeeTableRecords([$project])
        ->assertCanNotSeeTableRecords(Project::where('title', 'Other Project')->get());
});

test('admin can delete project from table', function () {
    $client = Client::factory()->create();

    $project = Project::factory()
        ->for($client)
        ->create();

    livewire(ProjectResource\Pages\ListProjects::class)
        ->callTableAction(DeleteAction::class, $project);

    $this->assertSoftDeleted($project);
});