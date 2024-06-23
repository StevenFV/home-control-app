<?php

namespace Database\Seeders;

use App\Enums\PermissionName;
use App\Enums\PermissionRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->resetCachedRolesPermissions();

        $roles = $this->createRoles();
        $permissions = $this->createPermissions();

        foreach ($roles as $roleName) {
            $this->assignPermissionsToRole($roleName, $permissions);
        }

        $this->seedAdminUser();
    }

    private function resetCachedRolesPermissions(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function createRoles(): array
    {
        $roles = PermissionRole::values();

        return array_map(function ($role) {
            return Role::create(['name' => $role])->name;
        }, $roles);
    }

    private function createPermissions(): array
    {
        $permissions = PermissionName::values();

        return array_map(function ($permission) {
            return Permission::create(['name' => $permission])->name;
        }, $permissions);
    }

    private function assignPermissionsToRole(string $roleName, array $permissions): void
    {
        $role = Role::findByName($roleName);
        $role->givePermissionTo($permissions);
    }

    private function seedAdminUser(): void
    {
        User::factory(1)->assignAdminRole()->create();
    }
}
