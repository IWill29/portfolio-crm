<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Izveido galveno administratoru un testa menedžeri.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@solostream.lv'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
            ]
        );

        $admin->syncRoles(['admin']);

        $manager = User::query()->updateOrCreate(
            ['email' => 'maris@solostream.lv'],
            [
                'name' => 'Menedžeris Māris',
                'password' => Hash::make('password123'),
            ]
        );

        $manager->syncRoles(['manager']);
    }
}
