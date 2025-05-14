<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create regular users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);

        // Role assignments will be handled by RolesAndPermissionsSeeder
        // We rely on RolesAndPermissionsSeeder to be run after this one
        // but we can still assign default roles if they exist
        
        if (Role::where('name', 'admin')->exists()) {
            $admin->assignRole('admin');
        }
        
        if (Role::where('name', 'user')->exists()) {
            $user1->assignRole('user');
            $user2->assignRole('user');
        }

        // Create additional users with factory if needed
        // User::factory(10)->create();
    }
} 