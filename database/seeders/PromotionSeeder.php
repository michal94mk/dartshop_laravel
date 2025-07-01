<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\Product;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products
        $products = Product::all();
        
        if ($products->count() < 5) {
            $this->command->info('Need at least 5 products in database');
            return;
        }

        // Promotion 1: Big Dart Sale
        $promotion1 = Promotion::create([
            'title' => 'Big Dart Sale',
            'name' => 'big_dart_sale',
            'code' => 'DART25',
            'description' => 'Best dart products at promotional prices! Don\'t miss out on professional equipment.',
            'discount_type' => 'percentage',
            'discount_value' => 25,
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'is_active' => true
        ]);

        // Promotion 2: End of Month Sale
        $promotion2 = Promotion::create([
            'title' => 'End of Month Sale',
            'name' => 'end_of_month_sale',
            'code' => 'MONTH50',
            'description' => 'Last chance to buy at low prices! Selected products with discount.',
            'discount_type' => 'fixed',
            'discount_value' => 50,
            'starts_at' => now(),
            'ends_at' => now()->addDays(3),
            'is_active' => true
        ]);

        // Promotion 3: New Products Sale
        $promotion3 = Promotion::create([
            'title' => 'New Products Sale',
            'name' => 'new_products_sale',
            'code' => 'NEW15',
            'description' => 'Newest products in our offer now available at promotional prices!',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'starts_at' => now(),
            'ends_at' => now()->addDays(14),
            'is_active' => true
        ]);

        $this->command->info('Created 3 sample promotions');
    }
}
