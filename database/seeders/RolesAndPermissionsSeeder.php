<?php

namespace Database\Seeders;

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

        // Create permissions
        $permissions = [
            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Brands
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',
            
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Promotions
            'view promotions',
            'create promotions',
            'edit promotions',
            'delete promotions',
            
            // Contact
            'manage contacts',
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Create roles and assign permissions if they don't exist
        if (!Role::where('name', 'admin')->exists()) {
            $role = Role::create(['name' => 'admin']);
            $role->givePermissionTo(Permission::all());
        } else {
            $role = Role::where('name', 'admin')->first();
            $role->syncPermissions(Permission::all());
        }
        
        if (!Role::where('name', 'user')->exists()) {
            $role = Role::create(['name' => 'user']);
            $role->givePermissionTo(['view products', 'view categories', 'view brands', 'view promotions']);
        } else {
            $role = Role::where('name', 'user')->first();
            $role->syncPermissions(['view products', 'view categories', 'view brands', 'view promotions']);
        }
        
        // Migrate existing users (assign roles based on current 'role' column)
        $users = User::all();
        
        foreach ($users as $user) {
            if (!$user->roles()->count()) { // Only assign roles if user doesn't have any
                if ($user->role === 'admin') {
                    $user->assignRole('admin');
                } else {
                    $user->assignRole('user');
                }
            }
        }
    }
} 