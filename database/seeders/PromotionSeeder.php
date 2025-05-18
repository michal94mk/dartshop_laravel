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
                'type' => 'percentage',
                'value' => 10,
                'minimum_order_amount' => 100,
                'usage_limit' => 1000,
                'usage_count' => 45,
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(2),
                'is_active' => true
            ],
            [
                'name' => 'Letnia Wyprzedaż',
                'code' => 'SUMMER20',
                'description' => 'Rabat 20% na wszystkie produkty letnie',
                'type' => 'percentage',
                'value' => 20,
                'minimum_order_amount' => 150,
                'usage_limit' => 500,
                'usage_count' => 125,
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date' => Carbon::now()->addDays(30),
                'is_active' => true
            ],
            [
                'name' => 'Darmowa Dostawa',
                'code' => 'FREEDEL',
                'description' => 'Darmowa dostawa na każde zamówienie',
                'type' => 'fixed_amount',
                'value' => 20,
                'minimum_order_amount' => 200,
                'usage_limit' => 300,
                'usage_count' => 85,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'is_active' => true
            ],
            [
                'name' => 'Rabat dla VIP',
                'code' => 'VIP15',
                'description' => 'Specjalny rabat dla stałych klientów',
                'type' => 'percentage',
                'value' => 15,
                'minimum_order_amount' => null,
                'usage_limit' => null,
                'usage_count' => 27,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(3),
                'is_active' => true
            ],
            [
                'name' => 'Wygasła Promocja',
                'code' => 'EXPIRED30',
                'description' => 'Promocja która już wygasła',
                'type' => 'percentage',
                'value' => 30,
                'minimum_order_amount' => 250,
                'usage_limit' => 100,
                'usage_count' => 100,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->subDays(5),
                'is_active' => false
            ]
        ];
        
        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
        
        $this->command->info('Promotions created successfully!');
    }
}
