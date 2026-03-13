<?php

use Appilament\Resources\ProjectResource;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use function Pest\Livewire\livewire;

beforeEach(function () {
    // Testa lietotājs, kas var piekļūt Filament panelim
    $this->actingAs(User::factory()->create());
});

it('renders the project listing page', function () {
    ProjectResource\Pages\ListProjects::route('/');

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertSuccessful();
});

it('lists projects in the table', function () {
    $client = Client::factory()->create();

    $projects = Project::factory()
        ->count(3)
        ->for($client)
        ->create();

    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertCanSeeTableRecords($projects)
        ->assertCountTableRecords(3);
});

it('shows/hides budget column based on permission', function () {
    $client = Client::factory()->create();

    Project::factory()->for($client)->create();

    Gate::define('view financial data', fn () => false);
    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableColumnHidden('budget');

    Gate::define('view financial data', fn () => true);
    livewire(ProjectResource\Pages\ListProjects::class)
        ->assertTableColumnVisible('budget');
});

it('can create a project via the form', function () {
    $client = Client::factory()->create();

    $newData = Project::factory()->make();

    livewire(ProjectResource\Pages\CreateProject::class)
        ->fillForm([
            'title' => $newData->title,
            'client_id' => $client->id,
            'status' => $newData->status,
            'priority' => $newData->priority,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('projects', [
        'title' => $newData->title,
        'client_id' => $client->id,
    ]);
});

it('validates required fields on create', function () {
    livewire(ProjectResource\Pages\CreateProject::class)
        ->fillForm(['title' => null])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});
