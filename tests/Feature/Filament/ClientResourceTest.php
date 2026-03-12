<?php

use App\Filament\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Actions\DeleteAction;
use function Pest\Livewire\livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    
    // Izveidojam Admin lietotāju, jo tikai viņš drīkst redzēt CRM
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    
    $this->actingAs($this->admin);
});

test('can render client list page', function () {
    // Pārbauda, vai lapa vispār atveras (200 OK)
    livewire(ClientResource\Pages\ListClients::class)
        ->assertSuccessful();
});

test('can list clients', function () {
    // Izveidojam testa klientus
    $clients = Client::factory()->count(3)->create();

    // Pārbauda, vai tabulā parādās klientu vārdi
    livewire(ClientResource\Pages\ListClients::class)
        ->assertCanSeeTableRecords($clients)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('email');
});

test('can search clients by name', function () {
    $client = Client::factory()->create(['name' => 'Unique Name']);
    Client::factory()->create(['name' => 'Other Name']);

    // Simulējam meklēšanu tabulā
    livewire(ClientResource\Pages\ListClients::class)
        ->searchTable('Unique Name')
        ->assertCanSeeTableRecords([$client])
        ->assertCanNotSeeTableRecords(Client::where('name', 'Other Name')->get());
});

test('admin can delete client from table', function () {
    $client = Client::factory()->create();

    // Simulējam Delete pogas nospiešanu tabulā
    livewire(ClientResource\Pages\ListClients::class)
        ->callTableAction(DeleteAction::class, $client);

    $this->assertSoftDeleted($client);
});
