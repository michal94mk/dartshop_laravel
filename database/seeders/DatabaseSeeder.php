<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed categories as they are referenced by products
        $this->call(CategorySeeder::class);
        
        // Then seed brands as they are also referenced by products
        $this->call(BrandSeeder::class);
        
        // Finally, seed products as they depend on both categories and brands
        $this->call(ProductSeeder::class);
        
        // 1. Roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);
        
        // 2. Base lookup tables
        $this->call([
            AboutUsSeeder::class,
            PrivacyPolicySeeder::class,
            TermsOfServiceSeeder::class,
        ]);
        
        // 3. User-dependent data
        $this->call([
            UserSeeder::class,
        ]);
        
        // 4. Related data
        $this->call([
            ShippingAddressSeeder::class,
            ReviewsTableSeeder::class,
            PromotionSeeder::class,
            NewsletterSeeder::class,
            ContactMessageSeeder::class,
        ]);
        
        // 5. Complex dependencies
        $this->call([
            TutorialSeeder::class,
            OrderSeeder::class, // Optional: Generates test order data for admin demo
        ]);
    }
}