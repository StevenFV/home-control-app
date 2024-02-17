<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\PermissionRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        Role::create(['name' => PermissionRole::ADMIN->value]);

        // Create permissions
        Permission::create(['name' => PermissionName::CONTROL_LIGHTING->value]);
        Permission::create(['name' => PermissionName::VIEW_LIGHTING->value]);

        $admin = Role::findByName(PermissionRole::ADMIN->value);
        $admin->givePermissionTo([
            PermissionName::CONTROL_LIGHTING->value,
            PermissionName::VIEW_LIGHTING->value,
        ]);
    }
}
