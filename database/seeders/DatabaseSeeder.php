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
        // Always run the roles and permissions seeder first
        $this->call(RolesAndPermissionsSeeder::class);

        // Then seed other data in order of dependencies
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
            ShippingAddressSeeder::class,
            ContactMessageSeeder::class,
            AboutPageSeeder::class,
            PromotionSeeder::class,
            ReviewsTableSeeder::class,
            OrderSeeder::class,
            TutorialSeeder::class,
        ]);
    }
}