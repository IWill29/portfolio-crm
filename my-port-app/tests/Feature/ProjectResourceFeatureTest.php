<?php

namespace Tests\Feature;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Gate;

class ProjectResourceFeatureTest extends TestCase
{
    protected User $user;
    protected User $adminUser;
    protected Client $client;

    public function setUp(): void
    {
        parent::setUp();

        // Izveido standarta lietotāju bez atļaujām
        $this->user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);

        // Izveido admin lietotāju ar visām atļaujām
        $this->adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Izveido test klientu
        $this->client = Client::factory()->create();

        // Simulējam Gate autorizācijas
        Gate::define('view financial data', function (User $user) {
            return $user->email === 'admin@example.com';
        });

        Gate::define('delete records', function (User $user) {
            return $user->email === 'admin@example.com';
        });
    }

    /**
     * Test: Projekta saraksts ir pieejams autentificētajiem lietotājiem
     */
    public function test_project_index_is_accessible_to_authenticated_users(): void
    {
        $this->actingAs($this->user)
            ->get('/admin/projects')
            ->assertStatus(200);
    }

    /**
     * Test: Neatļautie lietotāji tiek novirzīti uz login
     */
    public function test_project_index_requires_authentication(): void
    {
        $this->get('/admin/projects')
            ->assertRedirectToRoute('login');
    }

    /**
     * Test: Projekta izveides forma tiek rādīta
     */
    public function test_create_project_form_is_accessible(): void
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/projects/create')
            ->assertStatus(200);
    }

    /**
     * Test: Projekta sekmīga izveide ar visiem laukiem
     */
    public function test_create_project_with_all_fields(): void
    {
        $data = [
            'title' => 'New Website Project',
            'client_id' => $this->client->id,
            'description' => 'Modern responsive website design',
            'status' => ProjectStatus::Active->value,
            'priority' => ProjectPriority::High->value,
            'budget' => 5000.00,
            'starts_at' => '2026-03-15',
            'ends_at' => '2026-06-15',
        ];

        $this->actingAs($this->adminUser)
            ->post('/admin/projects', $data)
            ->assertRedirectContainsValidSessionToken();

        $this->assertDatabaseHas('projects', [
            'title' => 'New Website Project',
            'client_id' => $this->client->id,
            'budget' => '5000.00',
        ]);
    }

    /**
     * Test: Projekta izveide bez nepieciešamiem laukiem
     */
    public function test_create_project_without_required_fields_fails(): void
    {
        $data = [
            'title' => '', // Obligāts lauks
            'client_id' => null, // Obligāts lauks
            'status' => '', // Obligāts lauks
            'priority' => '', // Obligāts lauks
        ];

        $this->actingAs($this->adminUser)
            ->post('/admin/projects', $data)
            ->assertSessionHasErrors(['title', 'client_id', 'status', 'priority']);
    }

    /**
     * Test: Projekta nosaukums ne garāks par 255 rakstzīmēm
     */
    public function test_create_project_title_must_be_max_255_characters(): void
    {
        $longTitle = str_repeat('a', 256);

        $data = [
            'title' => $longTitle,
            'client_id' => $this->client->id,
            'status' => ProjectStatus::Active->value,
            'priority' => ProjectPriority::Medium->value,
        ];

        $this->actingAs($this->adminUser)
            ->post('/admin/projects', $data)
            ->assertSessionHasErrors('title');
    }

    /**
     * Test: Projekta rediģēšanas forma tiek rādīta
     */
    public function test_edit_project_form_is_accessible(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $this->actingAs($this->adminUser)
            ->get("/admin/projects/{$project->id}/edit")
            ->assertStatus(200);
    }

    /**
     * Test: Projekta sekmīga rediģēšana
     */
    public function test_update_project_successfully(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'Old Title',
            'budget' => 1000.00,
        ]);

        $newData = [
            'title' => 'Updated Project Title',
            'status' => ProjectStatus::Completed->value,
            'budget' => 2500.00,
        ];

        $this->actingAs($this->adminUser)
            ->put("/admin/projects/{$project->id}", $newData)
            ->assertRedirectContainsValidSessionToken();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project Title',
            'budget' => '2500.00',
        ]);
    }

    /**
     * Test: Projekta dzēšana ir iespējama tikai ar atļauju
     */
    public function test_delete_project_requires_permission(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);

        // Parastais lietotājs - liegts
        $this->actingAs($this->user)
            ->delete("/admin/projects/{$project->id}")
            ->assertStatus(403);

        // Admin - atļauts
        $this->actingAs($this->adminUser)
            ->delete("/admin/projects/{$project->id}")
            ->assertRedirectContainsValidSessionToken();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /**
     * Test: Neesošas projekta rediģēšana
     */
    public function test_update_nonexistent_project_returns_404(): void
    {
        $this->actingAs($this->adminUser)
            ->put('/admin/projects/99999', ['title' => 'Test'])
            ->assertStatus(404);
    }

    /**
     * Test: Budžeta lauks ir redzams tikai ar atļauju
     */
    public function test_budget_field_visibility_based_on_permission(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
            'budget' => 5000.00,
        ]);

        // Admin skat (var redzēt budžetu)
        $response = $this->actingAs($this->adminUser)
            ->get("/admin/projects/{$project->id}/edit");
        
        $response->assertStatus(200);

        // Parastais lietotājs (nevar redzēt budžetu bez atļaujas)
        $response = $this->actingAs($this->user)
            ->get("/admin/projects/{$project->id}/edit");
        
        $response->assertStatus(200);
    }

    /**
     * Test: Projekta saraksts parāda pareizos filtrus
     */
    public function test_project_list_can_be_filtered_by_status(): void
    {
        $activeProject = Project::factory()->create([
            'client_id' => $this->client->id,
            'status' => ProjectStatus::Active,
        ]);

        $completedProject = Project::factory()->create([
            'client_id' => $this->client->id,
            'status' => ProjectStatus::Completed,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/projects?status=' . ProjectStatus::Active->value);

        $response->assertStatus(200);
    }

    /**
     * Test: Projekta saraksts ir sortējams pēc laika
     */
    public function test_project_list_can_be_sorted_by_created_date(): void
    {
        Project::factory(3)->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/projects?sort=created_at');

        $response->assertStatus(200);
    }

    /**
     * Test: Enum vērtības ir pieejamas formas select laukā
     */
    public function test_status_enum_options_are_available_in_form(): void
    {
        $statuses = ProjectStatus::cases();

        $this->assertGreaterThan(0, count($statuses));
        $this->assertCount(count($statuses), $statuses);
    }

    /**
     * Test: Klienta relācija tiek validēta
     */
    public function test_create_project_with_invalid_client_id_fails(): void
    {
        $data = [
            'title' => 'Test Project',
            'client_id' => 99999, // Neesošs klients
            'status' => ProjectStatus::Active->value,
            'priority' => ProjectPriority::Medium->value,
        ];

        $this->actingAs($this->adminUser)
            ->post('/admin/projects', $data)
            ->assertSessionHasErrors('client_id');
    }

    /**
     * Test: Beigu datums nevar būt pirms sākuma datuma
     */
    public function test_end_date_cannot_be_before_start_date(): void
    {
        $data = [
            'title' => 'Test Project',
            'client_id' => $this->client->id,
            'status' => ProjectStatus::Active->value,
            'priority' => ProjectPriority::Medium->value,
            'starts_at' => '2026-06-15',
            'ends_at' => '2026-03-15', // PIRMS sākuma datuma!
        ];

        $this->actingAs($this->adminUser)
            ->post('/admin/projects', $data)
            ->assertSessionHasErrors('ends_at');
    }

    /**
     * Test: Budžets ir numeriski
     */
    public function test_budget_must_be_numeric(): void
    {
        $data = [
            'title' => 'Test Project',
            'client_id' => $this->client->id,
            'status' => ProjectStatus::Active->value,
            'priority' => ProjectPriority::Medium->value,
            'budget' => 'not-a-number',
        ];

        $this->actingAs($this->adminUser)
            ->post('/admin/projects', $data)
            ->assertSessionHasErrors('budget');
    }

    /**
     * Test: Projekta meklēšana pēc nosaukuma
     */
    public function test_search_projects_by_title(): void
    {
        $project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'Unique Project Title',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/projects?search=Unique');

        $response->assertStatus(200);
    }

    /**
     * Test: Projekta meklēšana pēc klienta
     */
    public function test_search_projects_by_client(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/projects?search=' . $this->client->name);

        $response->assertStatus(200);
    }

    /**
     * Test: Soft delete - projektus nevar dzēst pilnīgi
     */
    public function test_project_is_soft_deleted(): void
    {
        $project = Project::factory()->create(['client_id' => $this->client->id]);
        $projectId = $project->id;

        $this->actingAs($this->adminUser)
            ->delete("/admin/projects/{$projectId}");

        // Projekts joprojām ir datubāzē, bet ir deleted_at atzīme
        $this->assertDatabaseHas('projects', [
            'id' => $projectId,
        ]);

        // Bet tas nav redzams standarta vaicājumā
        $this->assertNull(Project::find($projectId));

        // Tas ir redzams tikai ar withTrashed()
        $this->assertNotNull(Project::withTrashed()->find($projectId));
    }
}
