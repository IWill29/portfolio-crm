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
        // 1. Izveidojam Galveno Adminu (Tevis pašam)
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@solostream.lv',
            'password' => Hash::make('password123'), // Vienmēr šifrējam paroli!
        ]);
        
        // Piešķiram lomu (izmantojot Spatie HasRoles trait)
        $admin->assignRole('admin');

        // 2. Izveidojam testa Menedžeri (portfolio demonstrācijai)
        $manager = User::create([
            'name'     => 'Menedžeris Māris',
            'email'    => 'maris@solostream.lv',
            'password' => Hash::make('password123'),
        ]);

        $manager->assignRole('manager');
    }
}
