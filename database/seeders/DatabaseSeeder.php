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
        // 1. Zawsze rozpoczynamy od ról i uprawnień
        $this->call(RolesAndPermissionsSeeder::class);
        
        // 2. Najpierw seedujemy tabele bez zależności
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            AboutUsSeeder::class,
        ]);
        
        // 3. Seedujemy tabele z podstawowymi zależnościami
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
        ]);
        
        // 4. Seedujemy tabele z zależnościami do powyższych tabel
        $this->call([
            ShippingAddressSeeder::class,
            ReviewsTableSeeder::class,
            ContactMessageSeeder::class,
            PromotionSeeder::class,
        ]);
        
        // 5. Na koniec seedujemy tabele z najbardziej złożonymi zależnościami
        $this->call([
            OrderSeeder::class,
            TutorialSeeder::class,
        ]);

        $this->call([
            NewsletterSeeder::class,
            PrivacyPolicySeeder::class,
            TermsOfServiceSeeder::class,
        ]);
    }
}