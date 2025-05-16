<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $customer = Role::firstOrCreate(['name' => 'customer']);
        
        // Define permissions
        $manageProducts = Permission::firstOrCreate(['name' => 'manage products']);
        $manageOrders = Permission::firstOrCreate(['name' => 'manage orders']);
        $manageUsers = Permission::firstOrCreate(['name' => 'manage users']);
        $manageCategories = Permission::firstOrCreate(['name' => 'manage categories']);
        $manageBrands = Permission::firstOrCreate(['name' => 'manage brands']);
        $managePromotions = Permission::firstOrCreate(['name' => 'manage promotions']);
        $manageReviews = Permission::firstOrCreate(['name' => 'manage reviews']);
        
        $viewOrders = Permission::firstOrCreate(['name' => 'view own orders']);
        $submitReviews = Permission::firstOrCreate(['name' => 'submit reviews']);
        $updateProfile = Permission::firstOrCreate(['name' => 'update profile']);
        
        // Assign permissions to roles
        $admin->givePermissionTo([
            $manageProducts,
            $manageOrders,
            $manageUsers,
            $manageCategories,
            $manageBrands,
            $managePromotions,
            $manageReviews,
            $viewOrders,
            $updateProfile
        ]);
        
        $customer->givePermissionTo([
            $viewOrders,
            $submitReviews,
            $updateProfile
        ]);
    }
} 