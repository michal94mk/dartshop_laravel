<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions from enum
        foreach (PermissionEnum::values() as $permission) {
            // Check if permission already exists
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Create roles and assign permissions
        foreach (RoleEnum::cases() as $roleEnum) {
            $roleName = $roleEnum->value;
            
            // Check if role already exists
            if (!Role::where('name', $roleName)->exists()) {
                $role = Role::create(['name' => $roleName]);
            } else {
                $role = Role::where('name', $roleName)->first();
            }
            
            // Assign the permissions
            $role->syncPermissions($roleEnum->permissions());
        }
        
        // Assign role to admin user if it exists
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->assignRole(RoleEnum::Admin->value);
        }
        
        // Assign regular role to users who don't have one
        $regularUsers = User::whereDoesntHave('roles')->get();
        foreach ($regularUsers as $user) {
            $user->assignRole(RoleEnum::User->value);
        }
    }
} 