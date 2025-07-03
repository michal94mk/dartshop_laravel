<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('Password123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role if it exists
        if ($admin && method_exists($admin, 'hasRole')) {
            if (!$admin->hasRole('admin')) {
                try {
                    $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                    $admin->assignRole($adminRole);
                } catch (\Exception $e) {
                    // Role assignment failed, but user still has is_admin = true
                    Log::warning('Could not assign admin role to user: ' . $e->getMessage());
                }
            }
        }

        // Regular user
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'first_name' => 'Regular',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Assign user role if it exists
        if ($user && method_exists($user, 'hasRole')) {
            if (!$user->hasRole('user')) {
                try {
                    $userRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
                    $user->assignRole($userRole);
                } catch (\Exception $e) {
                    // Role assignment failed
                    Log::warning('Could not assign user role to user: ' . $e->getMessage());
                }
            }
        }

        // Test user
        $testUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Assign user role if it exists
        if ($testUser && method_exists($testUser, 'hasRole')) {
            if (!$testUser->hasRole('user')) {
                try {
                    $userRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
                    $testUser->assignRole($userRole);
                } catch (\Exception $e) {
                    // Role assignment failed
                    Log::warning('Could not assign user role to test user: ' . $e->getMessage());
                }
            }
        }

        // Update all existing admin users to have admin role
        $adminUsers = User::where('is_admin', true)->get();
        foreach ($adminUsers as $adminUser) {
            if (method_exists($adminUser, 'hasRole') && !$adminUser->hasRole('admin')) {
                try {
                    $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                    $adminUser->assignRole($adminRole);
                } catch (\Exception $e) {
                    Log::warning('Could not assign admin role to existing admin user: ' . $e->getMessage());
                }
            }
        }

        // Update all existing non-admin users to have user role
        $regularUsers = User::where('is_admin', false)->get();
        foreach ($regularUsers as $regularUser) {
            if (method_exists($regularUser, 'hasRole') && !$regularUser->hasRole('user')) {
                try {
                    $userRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
                    $regularUser->assignRole($userRole);
                } catch (\Exception $e) {
                    Log::warning('Could not assign user role to existing regular user: ' . $e->getMessage());
                }
            }
        }

        // Additional realistic users for reviews
        $realisticUsers = [
            [
                'name' => 'Anna Kowalska',
                'first_name' => 'Anna',
                'last_name' => 'Kowalska',
                'email' => 'anna.kowalska@example.com'
            ],
            [
                'name' => 'Piotr Nowak',
                'first_name' => 'Piotr',
                'last_name' => 'Nowak',
                'email' => 'piotr.nowak@example.com'
            ],
            [
                'name' => 'Katarzyna Wiśniewska',
                'first_name' => 'Katarzyna',
                'last_name' => 'Wiśniewska',
                'email' => 'katarzyna.wisniewska@example.com'
            ],
            [
                'name' => 'Marek Adamski',
                'first_name' => 'Marek',
                'last_name' => 'Adamski',
                'email' => 'marek.adamski@example.com'
            ],
            [
                'name' => 'Aleksandra Jankowska',
                'first_name' => 'Aleksandra',
                'last_name' => 'Jankowska',
                'email' => 'aleksandra.jankowska@example.com'
            ],
            [
                'name' => 'Tomasz Krawczyk',
                'first_name' => 'Tomasz',
                'last_name' => 'Krawczyk',
                'email' => 'tomasz.krawczyk@example.com'
            ],
            [
                'name' => 'Monika Pawłowska',
                'first_name' => 'Monika',
                'last_name' => 'Pawłowska',
                'email' => 'monika.pawlowska@example.com'
            ],
            [
                'name' => 'Michał Dąbrowski',
                'first_name' => 'Michał',
                'last_name' => 'Dąbrowski',
                'email' => 'michal.dabrowski@example.com'
            ]
        ];

        foreach ($realisticUsers as $userData) {
            $realisticUser = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'email_verified_at' => now(),
                ]
            );

            // Assign user role if it exists
            if ($realisticUser && method_exists($realisticUser, 'hasRole')) {
                if (!$realisticUser->hasRole('user')) {
                    try {
                        $userRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
                        $realisticUser->assignRole($userRole);
                    } catch (\Exception $e) {
                        Log::warning('Could not assign user role to realistic user: ' . $e->getMessage());
                    }
                }
            }
        }
    }
} 