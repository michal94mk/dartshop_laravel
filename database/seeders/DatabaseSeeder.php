<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Important to maintain the correct order due to foreign key constraints
        $this->call([
            UserSeeder::class,      // Users first (no dependencies)
            CategorySeeder::class,  // Categories next (no dependencies)
            BrandSeeder::class,     // Brands next (no dependencies)
            ProductSeeder::class,   // Products last (depends on categories and brands)
        ]);
    }
}
