<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            // Tarcze
            [
                'name' => 'Winmau Blade 6 Triple Core Carbon',
                'description' => 'Profesjonalna tarcza turniejowa z potrójnym rdzeniem i włóknem węglowym. Cieńsze druty redukują odbicia, a system Dual Core zapewnia większą trwałość.',
                'price' => 89.95,
                'category_id' => 2, // Dartboards/Tarcze
                'brand_id' => 4, // Winmau
                'images' => 'winmau-blade-6-triple-core-carbon.jpg',
                'stock' => 10,
                'is_featured' => true,
                'is_active' => true
            ],
            [
                'name' => 'Target Aspar',
                'description' => 'Wysokiej jakości tarcza turniejowa z cienkimi drutami i zwiększoną powierzchnią wbijania.',
                'price' => 44.95,
                'category_id' => 2, // Dartboards/Tarcze
                'brand_id' => 3, // Target
                'images' => 'target-aspar.jpg',
                'stock' => 15,
                'is_featured' => false,
                'is_active' => true
            ],
            [
                'name' => 'Unicorn Eclipse Ultra',
                'description' => 'Profesjonalna tarcza turniejowa z systemem Eclipse HD. Cienkie druty i wyraźny kontrast kolorów dla lepszej widoczności.',
                'price' => 59.95,
                'category_id' => 2, // Dartboards/Tarcze
                'brand_id' => 1, // Unicorn
                'images' => 'unicorn-eclipse-ultra.jpg',
                'stock' => 8,
                'is_featured' => true,
                'is_active' => true
            ],
            
            // Lotki Steel
            [
                'name' => 'Target Vapor 8 Black',
                'description' => 'Lotki z 80% wolframu z charakterystycznym czarnym wykończeniem i unikalnym systemem uchwytu. Dostępne w wersjach z kolorowymi akcentami.',
                'price' => 56.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 3, // Target
                'images' => 'target-vapor-8-black.jpg',
                'stock' => 12,
                'is_featured' => true,
                'is_active' => true
            ],
            [
                'name' => 'Red Dragon Amberjack 25g',
                'description' => 'Profesjonalne lotki z 90% wolframu z charakterystycznym pomarańczowo-czarnym wzorem grippowym. Wyjątkowa precyzja i kontrola.',
                'price' => 37.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 5, // Red Dragon
                'images' => 'red-dragon-amberjack-25g.jpg',
                'stock' => 20,
                'is_featured' => false,
                'is_active' => true
            ],
            [
                'name' => 'Winmau Blackout',
                'description' => 'Eleganckie czarne lotki z 90% wolframu z matowym wykończeniem i precyzyjnym systemem gripowym.',
                'price' => 49.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 4, // Winmau
                'images' => 'winmau-blackout.jpg',
                'stock' => 18,
                'is_featured' => false,
                'is_active' => true
            ],
            
            // Lotki Soft
            [
                'name' => 'Target Bolide 01',
                'description' => 'Lotki softip z 90% wolframu zaprojektowane dla maksymalnej kontroli i precyzji.',
                'price' => 59.95,
                'category_id' => 1, // Darts/Lotki 
                'brand_id' => 3, // Target
                'images' => 'target-bolide-01.jpg',
                'stock' => 9,
                'is_featured' => true,
                'is_active' => true
            ],
            [
                'name' => 'Harrows Spina',
                'description' => 'Lotki softip z 90% wolframu z charakterystycznym spiralnym wzorem gripowym.',
                'price' => 44.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 2, // Harrows
                'images' => 'harrows-spina.jpg',
                'stock' => 14,
                'is_featured' => false,
                'is_active' => true
            ],
            
            // Akcesoria
            [
                'name' => 'Target Corona Vision Lighting System',
                'description' => 'Profesjonalny system oświetlenia tarczy eliminujący cienie i poprawiający widoczność.',
                'price' => 79.95,
                'category_id' => 3, // Accessories/Akcesoria
                'brand_id' => 3, // Target
                'images' => 'target-corona-vision.jpg',
                'stock' => 5,
                'is_featured' => true,
                'is_active' => true
            ],
            [
                'name' => 'Winmau Simon Whitlock Case',
                'description' => 'Profesjonalne etui na lotki z twardą obudową, sygnowane przez Simona Whitlocka.',
                'price' => 19.95,
                'category_id' => 5, // Cases/Etui
                'brand_id' => 4, // Winmau
                'images' => 'winmau-simon-whitlock-case.jpg',
                'stock' => 25,
                'is_featured' => false,
                'is_active' => true
            ],
            
            // Lotki
            [
                'name' => 'Mission Snakebite World Champion Edition',
                'description' => 'Lotki z 90% wolframu sygnowane przez mistrza świata PDC Petera Wrighta, z charakterystycznym wzorem wężowej skóry.',
                'price' => 69.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 7, // Mission
                'images' => 'mission-snakebite-champion.jpg',
                'stock' => 7,
                'is_featured' => true,
                'is_active' => true
            ],
            
            // Shafty
            [
                'name' => 'Target Pro Grip Shafts',
                'description' => 'Profesjonalne shafty z unikalnym systemem Pro Grip zapobiegającym luzowaniu się podczas gry.',
                'price' => 5.95,
                'category_id' => 7, // Shafts/Shafty
                'brand_id' => 3, // Target
                'images' => 'target-pro-grip-shafts.jpg',
                'stock' => 50,
                'is_featured' => false,
                'is_active' => true
            ],
            [
                'name' => 'Designa DSX Flights',
                'description' => 'Wytrzymałe i aerodynamiczne lotki dostępne w różnych kształtach i wzorach.',
                'price' => 2.95,
                'category_id' => 6, // Flights/Piórka
                'brand_id' => 9, // Designa
                'images' => 'designa-dsx-flights.jpg',
                'stock' => 100,
                'is_featured' => false,
                'is_active' => true
            ],
            [
                'name' => 'Bull\'s Dartmate Plus Scorepad',
                'description' => 'Profesjonalna tablica do zapisywania wyników z magnetycznym markerem.',
                'price' => 24.95,
                'category_id' => 3, // Accessories/Akcesoria
                'brand_id' => 8, // Bull's
                'images' => 'bulls-dartmate-plus.jpg',
                'stock' => 15,
                'is_featured' => false,
                'is_active' => true
            ],
            [
                'name' => 'One80 Reflex Points',
                'description' => 'Profesjonalne groty steel o zwiększonej wytrzymałości i zmniejszonej częstotliwości odbić.',
                'price' => 9.95,
                'category_id' => 3, // Accessories/Akcesoria
                'brand_id' => 10, // One80
                'images' => 'one80-reflex-points.jpg',
                'stock' => 40,
                'is_featured' => false,
                'is_active' => true
            ]
        ];

        foreach ($products as $index => $productData) {
            // Generate slug from name
            $productData['slug'] = Str::slug($productData['name']);
            
            // Generate unique SKU
            $productData['sku'] = 'DS-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT);
            
            Product::create($productData);
        }
    }
} 