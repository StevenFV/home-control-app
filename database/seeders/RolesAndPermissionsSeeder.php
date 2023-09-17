<?php

namespace Database\Seeders;

use App\Models\User;
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
        Role::create(['name' => 'super admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'maintenance']);

        // Create permissions
        Permission::create(['name' => 'control lighting']);
        Permission::create(['name' => 'control heating']);
        Permission::create(['name' => 'control ventilation']);
        Permission::create(['name' => 'control security']);
        Permission::create(['name' => 'control multimedia']);
        Permission::create(['name' => 'view device settings']);
        Permission::create(['name' => 'view system settings']);
        Permission::create(['name' => 'configure device settings']);
        Permission::create(['name' => 'configure system settings']);

        // Assign permissions to roles
        $superAdmin = Role::findByName('super admin');
        $superAdmin->givePermissionTo([
            'control lighting',
            'control heating',
            'control ventilation',
            'control security',
            'control multimedia',
            'configure device settings',
            'configure system settings'
        ]);

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'control lighting',
            'control heating',
            'control ventilation',
            'control security',
            'control multimedia',
            'view system settings',
            'configure device settings'
        ]);

        $user = Role::findByName('user');
        $user->givePermissionTo([
            'control lighting',
            'control heating',
            'control ventilation',
            'control security',
            'control multimedia'
        ]);

        $maintenance = Role::findByName('maintenance');
        $maintenance->givePermissionTo([
            'control lighting',
            'control heating',
            'control ventilation',
            'control security',
            'control multimedia',
            'view device settings',
            'view system settings'
        ]);
    }
}
