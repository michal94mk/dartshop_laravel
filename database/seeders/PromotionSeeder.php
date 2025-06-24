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
        // Pobierz wszystkie produkty
        $products = Product::all();
        
        if ($products->count() < 5) {
            $this->command->info('Potrzeba co najmniej 5 produktów w bazie danych');
            return;
        }

        // Promocja 1: Wielka Wyprzedaż Dart
        $promotion1 = Promotion::create([
            'title' => 'Wielka Wyprzedaż Dart',
            'name' => 'wielka_wyprzedaz_dart',
            'code' => 'DART25',
            'description' => 'Najlepsze produkty do dart w promocyjnych cenach! Nie przegap okazji na profesjonalny sprzęt.',
            'discount_type' => 'percentage',
            'discount_value' => 25,
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'is_active' => true
        ]);

        // Promocja 2: Wyprzedaż Końca Miesiąca
        $promotion2 = Promotion::create([
            'title' => 'Wyprzedaż Końca Miesiąca',
            'name' => 'wyprzedaz_konca_miesiaca',
            'code' => 'MONTH50',
            'description' => 'Ostatnia szansa na zakup w niskich cenach! Wybrane produkty z rabatem.',
            'discount_type' => 'fixed',
            'discount_value' => 50,
            'starts_at' => now(),
            'ends_at' => now()->addDays(3),
            'is_active' => true
        ]);

        // Promocja 3: Nowości w Promocji
        $promotion3 = Promotion::create([
            'title' => 'Nowości w Promocji',
            'name' => 'nowosci_w_promocji',
            'code' => 'NEW15',
            'description' => 'Najnowsze produkty w naszej ofercie już dostępne w promocyjnych cenach!',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'starts_at' => now(),
            'ends_at' => now()->addDays(14),
            'is_active' => true
        ]);

        $this->command->info('Utworzono 3 przykładowe promocje');
    }
}
