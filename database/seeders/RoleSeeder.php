<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Izveido sistēmas lomas un pamatatļaujas.
     */
    public function run(): void
    {
        // 1. Resetējam kešatmiņu (Spatie Permission labā prakse)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Izveidojam specifiskas atļaujas (Permissions)
        // Šīs mēs izmantosim vēlāk Policies failos
        $permissions = [
            'view financial data', // Tikai adminam
            'delete records',      // Tikai adminam
            'manage users',        // Tikai adminam
            'access crm',          // Visām lomām
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 3. Izveidojam lomas (Roles)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);

        // 4. Piešķiram atļaujas lomām
        $admin->givePermissionTo(Permission::all()); // Admins drīkst visu

        $manager->givePermissionTo([
            'access crm',
        ]);
    }
}
