<?php

use App\Models\User;
use App\Models\Client;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Pirms katra testa izpildām RoleSeeder, lai mums būtu lomas un tiesības
    $this->seed(RoleSeeder::class);
});

test('admin can delete a client', function () {
    // 1. Izveidojam adminu un piešķiram lomu
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // 2. Izveidojam klientu
    $client = Client::factory()->create();

    // 3. Pārbaudām, vai admins DRĪKST dzēst (izmantojot Policy)
    expect($admin->can('delete', $client))->toBeTrue();
});

test('manager cannot delete a client', function () {
    // 1. Izveidojam menedžeri un piešķiram lomu
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    // 2. Izveidojam klientu
    $client = Client::factory()->create();

    // 3. Pārbaudām, vai menedžeris NEDRĪKST dzēst
    expect($manager->can('delete', $client))->toBeFalse();
});

test('manager can view clients', function () {
    $manager = User::factory()->create();
    $manager->assignRole('manager');

    expect($manager->can('viewAny', Client::class))->toBeTrue();
});
