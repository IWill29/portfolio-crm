<?php

use App\Filament\Widgets\ProjectStatsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use function Pest\Livewire\livewire;
use App\Models\Client;
use Carbon\Carbon;
use App\Enums\ProjectStatus;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Filament\Facades\Filament;

beforeEach(function () {
    /** @var \Tests\TestCase $this */
    $this->seed(RoleSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Filament::auth()->login($admin);

    $this->actingAs($admin, Filament::getAuthGuard());
});

dataset('filamentWidgets', [
    RevenueChartWidget::class,
    ProjectStatsWidget::class,
    RecentActivityWidget::class,
]);

test('filament widgets render successfully', function (string $widgetClass) {
    livewire($widgetClass)
        ->assertSuccessful();
})->with('filamentWidgets');

test('revenue chart widget returns expected data structure', function () {
    $client = Client::factory()->create(); // <--- obligāts

    Project::factory()->for($client)->create(['budget' => 100]);
    Project::factory()->for($client)->create(['budget' => 200]);

    $widget = app(RevenueChartWidget::class);

    $getData = function (): array {
        return $this->getData();
    };

    $data = $getData->call($widget);

    expect($data)
        ->toBeArray()
        ->and($data)
        ->toHaveKey('labels')
        ->and($data)
        ->toHaveKey('datasets')
        ->and($data['datasets'][0])
        ->toHaveKey('data');
});

test('revenue chart widget returns expected data values', function () {
    Carbon::setTestNow(Carbon::parse('2026-03-17'));

    $client = Client::factory()->create();

    // Projekts 2 mēnešus agrāk (janvārī, ja "tagad" ir marts)
    Project::factory()->for($client)->create([
        'budget' => 100,
        'created_at' => Carbon::now()->subMonths(2),
    ]);

    // Projekts pagājušajā mēnesī
    Project::factory()->for($client)->create([
        'budget' => 200,
        'created_at' => Carbon::now()->subMonths(1),
    ]);

    // Projekts šomēnes
    Project::factory()->for($client)->create([
        'budget' => 50,
        'created_at' => Carbon::now(),
    ]);

    $widget = app(RevenueChartWidget::class);

    $getData = function (): array {
        return $this->getData();
    };

    $data = $getData->call($widget);

    expect($data['labels'])
        ->toBeArray()
        ->toHaveCount(6)
        ->toContain('Jan 2026')
        ->toContain('Feb 2026')
        ->toContain('Mar 2026');

    expect($data['datasets'][0]['data'])
        ->toBeArray()
        ->toContain(100)
        ->toContain(200)
        ->toContain(50);
});

test('project stats widget returns expected stats', function () {
    $client = Client::factory()->create();

    Project::factory()->for($client)->create([
        'status' => ProjectStatus::InProgress->value,
        'budget' => 100,
    ]);
    Project::factory()->for($client)->create([
        'status' => ProjectStatus::Done->value,
        'budget' => 300,
    ]);

    $widget = app(ProjectStatsWidget::class);

    $getStats = function (): array {
        return $this->getStats();
    };

    $stats = $getStats->call($widget);

    expect($stats)
        ->toBeArray()
        ->and(collect($stats)->map(fn ($stat) => $stat->getLabel()))->toContain('Visi projekti')
        ->and(collect($stats)->map(fn ($stat) => $stat->getLabel()))->toContain('Aktīvie projekti');

    // Opcija: pārbauda, ka kopējā summa un vidējais atbilst
    $totals = collect($stats)->keyBy(fn ($stat) => $stat->getLabel());

    expect($totals['Kopējais budžets']->getValue())->toBe('€400,00');
    expect($totals['Vidējais budžets']->getValue())->toBe('€200,00');
});

test('recent activity widget shows latest activity', function () {
    $user = User::factory()->create();

    activity()
        ->causedBy($user)
        ->performedOn($user)
        ->log('Test activity');

    livewire(RecentActivityWidget::class)
        ->assertSuccessful()
        ->assertSee('Test activity');
});

test('recent activity widget shows latest activity with livewire', function () {
    $user = User::factory()->create();

    activity()
        ->causedBy($user)
        ->performedOn($user)
        ->log('Test activity');

    $widget = livewire(RecentActivityWidget::class);

    $getData = function (): array {
        $query = $this->getTableQuery(); // → neeksistē uz Livewire wrapper

        return $query->get()->toArray();
    };

    $rows = $getData->call($widget);

    $firstRow = collect($rows)->first();

    expect($firstRow['description'])->toBe('Test activity');
    expect(collect($rows)->first())->toHaveKey('description');
});

dataset('discoveredFilamentWidgets', function () {
    $widgetsDirectory = realpath(__DIR__ . '/../../../app/Filament/Widgets');

    $files = glob($widgetsDirectory . '/*.php');

    return collect($files)
        ->map(fn ($path) => 'App\\Filament\\Widgets\\' . Str::studly(basename($path, '.php')))
        ->filter(fn ($class) => class_exists($class))
        ->all();
});

test('all filament widgets render successfully (auto-discovered)', function (string $widgetClass) {
    livewire($widgetClass)->assertSuccessful();
})->with('discoveredFilamentWidgets');

test('filament dashboard loads successfully', function () {
    config(['app.env' => 'local']);

    $this->get('/admin')
        ->assertOk()
        ->assertSee('Welcome')
        ->assertSee('Sign out');
});

test('activity log records model events and recent activity widget shows them', function () {
    $client = Client::factory()->create();

    $project = Project::factory()
        ->for($client)
        ->create(['title' => 'Test project']);

    $document = Document::factory()
        ->for($project)
        ->create(['title' => 'Specs']);

    // Trigger an update so we get created + updated entries
    $project->update(['budget' => 999]);

    $latest = Activity::latest()->take(3)->get();

    // Pārliecināmies, ka activity log satur ierakstus no mūsu modeļiem
    expect($latest->pluck('log_name')->unique()->sort()->values())
        ->toContain('Project')
        ->toContain('Document');

    // Pārliecināmies, ka widget rāda ierakstus ar mūsu “description”
    livewire(RecentActivityWidget::class)
        ->assertSuccessful()
        ->assertSee('Project "Test project" was created')
        ->assertSee('Document "Specs" was created');
});