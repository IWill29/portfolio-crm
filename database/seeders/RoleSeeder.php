<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Izveido sistēmas lomas un pamatatļaujas.
     */
    public function run(): void
    {
        // 1. Resetējam kešatmiņu (Spatie Permission labā prakse)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Izveidojam specifiskas atļaujas (Permissions)
        // Šīs mēs izmantosim vēlāk Policies failos
        $permissions = [
            'view financial data', // Tikai adminam
            'delete records',      // Tikai adminam
            'manage users',        // Tikai adminam
            'access crm',          // Visām lomām
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 3. Izveidojam lomas (Roles)
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);

        // 4. Piešķiram atļaujas lomām
        $admin->givePermissionTo(Permission::all()); // Admins drīkst visu
        
        $manager->givePermissionTo([
            'access crm',
        ]);
    }
}
