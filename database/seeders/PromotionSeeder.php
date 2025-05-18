<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample promotion codes
        $promotions = [
            [
                'name' => 'Promocja Powitalna',
                'code' => 'WELCOME10',
                'description' => 'Rabat 10% na pierwsze zakupy',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'minimum_order_value' => 100,
                'usage_limit' => 1000,
                'used_count' => 45,
                'starts_at' => Carbon::now()->subMonths(1),
                'ends_at' => Carbon::now()->addMonths(2),
                'is_active' => true
            ],
            [
                'name' => 'Letnia Wyprzedaż',
                'code' => 'SUMMER20',
                'description' => 'Rabat 20% na wszystkie produkty letnie',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'minimum_order_value' => 150,
                'usage_limit' => 500,
                'used_count' => 125,
                'starts_at' => Carbon::now()->startOfMonth(),
                'ends_at' => Carbon::now()->addDays(30),
                'is_active' => true
            ],
            [
                'name' => 'Darmowa Dostawa',
                'code' => 'FREEDEL',
                'description' => 'Darmowa dostawa na każde zamówienie',
                'discount_type' => 'fixed',
                'discount_value' => 20,
                'minimum_order_value' => 200,
                'usage_limit' => 300,
                'used_count' => 85,
                'starts_at' => Carbon::now()->subDays(15),
                'ends_at' => Carbon::now()->addDays(15),
                'is_active' => true
            ],
            [
                'name' => 'Rabat dla VIP',
                'code' => 'VIP15',
                'description' => 'Specjalny rabat dla stałych klientów',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'minimum_order_value' => null,
                'usage_limit' => null,
                'used_count' => 27,
                'starts_at' => Carbon::now()->subMonths(3),
                'ends_at' => Carbon::now()->addMonths(3),
                'is_active' => true
            ],
            [
                'name' => 'Wygasła Promocja',
                'code' => 'EXPIRED30',
                'description' => 'Promocja która już wygasła',
                'discount_type' => 'percentage',
                'discount_value' => 30,
                'minimum_order_value' => 250,
                'usage_limit' => 100,
                'used_count' => 100,
                'starts_at' => Carbon::now()->subMonths(3),
                'ends_at' => Carbon::now()->subDays(5),
                'is_active' => false
            ]
        ];
        
        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
        
        $this->command->info('Promotions created successfully!');
    }
}
