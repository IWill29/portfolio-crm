<?php

use App\Enums\ProjectStatus;
use App\Enums\ProjectPriority;
use App\Filament\Resources\ProjectResource;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use function Pest\Livewire\livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ProjectResource - List Projekti', function () {
    beforeEach(function () {
        $this->seed(RoleSeeder::class);
        
        // Izveidojam Admin lietotāju
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->actingAs($this->admin);
    });

    test('can render the project list page', function () {
        livewire(ProjectResource\Pages\ListProjects::class)
            ->assertSuccessful();
    });

    test('can list all projects with table columns', function () {
        $projects = Project::factory()->count(3)->create();

        livewire(ProjectResource\Pages\ListProjects::class)
            ->assertCanSeeTableRecords($projects)
            ->assertCountTableRecords(3)
            ->assertCanRenderTableColumn('title')
            ->assertCanRenderTableColumn('client.name')
            ->assertCanRenderTableColumn('status')
            ->assertCanRenderTableColumn('priority');
    });

    test('can search projects by title', function () {
        Project::factory()->create(['title' => 'Unikāls Projekts']);
        Project::factory()->create(['title' => 'Cits Projekts']);

        livewire(ProjectResource\Pages\ListProjects::class)
            ->searchTable('Unikāls')
            ->assertCanSeeTableRecords([
                Project::where('title', 'Unikāls Projekts')->first()
            ])
            ->assertCanNotSeeTableRecords([
                Project::where('title', 'Cits Projekts')->first()
            ]);
    });

    test('shows budget column when admin has financial permission', function () {
        livewire(ProjectResource\Pages\ListProjects::class)
            ->assertTableColumnVisible('budget');
    });

    test('hides budget column when user lacks financial permission', function () {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $this->actingAs($manager);

        livewire(ProjectResource\Pages\ListProjects::class)
            ->assertTableColumnHidden('budget');
    });

    test('can filter projects by status', function () {
        $inProgressProject = Project::factory()->create([
            'status' => ProjectStatus::InProgress
        ]);
        $doneProject = Project::factory()->create([
            'status' => ProjectStatus::Done
        ]);

        livewire(ProjectResource\Pages\ListProjects::class)
            ->filterTable('status', ProjectStatus::InProgress->value)
            ->assertCanSeeTableRecords([$inProgressProject])
            ->assertCanNotSeeTableRecords([$doneProject]);
    });

    test('admin can view edit action', function () {
        $project = Project::factory()->create();

        livewire(ProjectResource\Pages\ListProjects::class)
            ->callTableAction(EditAction::class, $project);
    });

    test('admin can delete project from table', function () {
        $project = Project::factory()->create();

        livewire(ProjectResource\Pages\ListProjects::class)
            ->callTableAction(DeleteAction::class, $project);

        $this->assertSoftDeleted($project);
    });

    test('manager cannot delete project', function () {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $this->actingAs($manager);

        $project = Project::factory()->create();

        livewire(ProjectResource\Pages\ListProjects::class)
            ->assertTableActionHidden(DeleteAction::class, $project);
    });
});

describe('ProjectResource - Create Projekti', function () {
    beforeEach(function () {
        $this->seed(RoleSeeder::class);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->actingAs($this->admin);
    });

    test('can render create project page', function () {
        livewire(ProjectResource\Pages\CreateProject::class)
            ->assertSuccessful();
    });

    test('can create project with all required fields', function () {
        $client = Client::factory()->create();
        $projectData = Project::factory()->make([
            'client_id' => $client->id
        ]);

        livewire(ProjectResource\Pages\CreateProject::class)
            ->fillForm([
                'title' => $projectData->title,
                'client_id' => $client->id,
                'description' => $projectData->description,
                'status' => ProjectStatus::Idea,
                'priority' => ProjectPriority::Medium,
                'starts_at' => '2026-04-01',
                'ends_at' => '2026-06-01',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', [
            'title' => $projectData->title,
            'client_id' => $client->id,
            'status' => ProjectStatus::Idea,
            'priority' => ProjectPriority::Medium,
        ]);
    });

    test('can create project with budget as admin', function () {
        $client = Client::factory()->create();

        livewire(ProjectResource\Pages\CreateProject::class)
            ->fillForm([
                'title' => 'Budžeta Projekts',
                'client_id' => $client->id,
                'status' => ProjectStatus::InProgress,
                'priority' => ProjectPriority::High,
                'budget' => 9999.99,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', [
            'title' => 'Budžeta Projekts',
            'budget' => 9999.99,
        ]);
    });

    test('validates required title field', function () {
        $client = Client::factory()->create();

        livewire(ProjectResource\Pages\CreateProject::class)
            ->fillForm([
                'title' => null,
                'client_id' => $client->id,
                'status' => ProjectStatus::Idea,
                'priority' => ProjectPriority::Medium,
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    });

    test('validates required client_id field', function () {
        livewire(ProjectResource\Pages\CreateProject::class)
            ->fillForm([
                'title' => 'Bez Klienta',
                'client_id' => null,
                'status' => ProjectStatus::Idea,
                'priority' => ProjectPriority::Medium,
            ])
            ->call('create')
            ->assertHasFormErrors(['client_id' => 'required']);
    });

    test('validates title max length', function () {
        $client = Client::factory()->create();
        $longTitle = str_repeat('A', 256);

        livewire(ProjectResource\Pages\CreateProject::class)
            ->fillForm([
                'title' => $longTitle,
                'client_id' => $client->id,
                'status' => ProjectStatus::Idea,
                'priority' => ProjectPriority::Medium,
            ])
            ->call('create')
            ->assertHasFormErrors(['title']);
    });

    test('validates numeric budget', function () {
        $client = Client::factory()->create();

        livewire(ProjectResource\Pages\CreateProject::class)
            ->fillForm([
                'title' => 'Nederīgs Budžets',
                'client_id' => $client->id,
                'status' => ProjectStatus::Idea,
                'priority' => ProjectPriority::Medium,
                'budget' => 'nevis skaitlis',
            ])
            ->call('create')
            ->assertHasFormErrors(['budget']);
    });

    test('manager can create project without budget field visibility', function () {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $this->actingAs($manager);

        $client = Client::factory()->create();

        livewire(ProjectResource\Pages\CreateProject::class)
            ->fillForm([
                'title' => 'Menedžera Projekts',
                'client_id' => $client->id,
                'status' => ProjectStatus::Idea,
                'priority' => ProjectPriority::Low,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', [
            'title' => 'Menedžera Projekts',
        ]);
    });
});

describe('ProjectResource - Edit Projekti', function () {
    beforeEach(function () {
        $this->seed(RoleSeeder::class);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->actingAs($this->admin);
    });

    test('can edit existing project', function () {
        $project = Project::factory()->create();
        $client = Client::factory()->create();

        livewire(ProjectResource\Pages\EditProject::class, ['record' => $project->id])
            ->fillForm([
                'title' => 'Atjaunināts Nosaukums',
                'client_id' => $client->id,
                'status' => ProjectStatus::Review,
                'priority' => ProjectPriority::Urgent,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($project->refresh())
            ->title->toBe('Atjaunināts Nosaukums')
            ->status->toBe(ProjectStatus::Review)
            ->priority->toBe(ProjectPriority::Urgent);
    });

    test('can update project budget as admin', function () {
        $project = Project::factory()->create(['budget' => 1000.00]);

        livewire(ProjectResource\Pages\EditProject::class, ['record' => $project->id])
            ->assertFormSet([
                'budget' => 1000.00,
            ])
            ->fillForm(['budget' => 5000.50])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($project->refresh()->budget)->toBe('5000.50');
    });

    test('can update project dates', function () {
        $project = Project::factory()->create();

        livewire(ProjectResource\Pages\EditProject::class, ['record' => $project->id])
            ->fillForm([
                'starts_at' => '2026-05-01',
                'ends_at' => '2026-08-31',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($project->refresh())
            ->starts_at->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('manager cannot see budget field in edit form', function () {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $this->actingAs($manager);

        $project = Project::factory()->create(['budget' => 5000.00]);

        livewire(ProjectResource\Pages\EditProject::class, ['record' => $project->id])
            ->assertFormComponentDisabled('budget');
    });

    test('validates required fields on update', function () {
        $project = Project::factory()->create();

        livewire(ProjectResource\Pages\EditProject::class, ['record' => $project->id])
            ->fillForm(['title' => null])
            ->call('save')
            ->assertHasFormErrors(['title' => 'required']);
    });
});

describe('ProjectResource - Authorization & Permissions', function () {
    beforeEach(function () {
        $this->seed(RoleSeeder::class);
    });

    test('admin can view financial data in projections', function () {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        expect($admin->can('view financial data'))->toBeTrue();
    });

    test('manager cannot view financial data', function () {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        expect($manager->can('view financial data'))->toBeFalse();
    });

    test('admin can delete records', function () {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        expect($admin->can('delete records'))->toBeTrue();
    });

    test('manager cannot delete records', function () {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        expect($manager->can('delete records'))->toBeFalse();
    });
});
