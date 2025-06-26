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
                'image' => 'product01.png',
            ],
            [
                'name' => 'Target Aspar',
                'description' => 'Wysokiej jakości tarcza turniejowa z cienkimi drutami i zwiększoną powierzchnią wbijania.',
                'price' => 44.95,
                'category_id' => 2, // Dartboards/Tarcze
                'brand_id' => 3, // Target
                'image' => 'product02.png',
            ],
            [
                'name' => 'Unicorn Eclipse Ultra',
                'description' => 'Profesjonalna tarcza turniejowa z systemem Eclipse HD. Cienkie druty i wyraźny kontrast kolorów dla lepszej widoczności.',
                'price' => 59.95,
                'category_id' => 2, // Dartboards/Tarcze
                'brand_id' => 1, // Unicorn
                'image' => 'product03.png',
            ],
            
            // Lotki Steel
            [
                'name' => 'Target Vapor 8 Black',
                'description' => 'Lotki z 80% wolframu z charakterystycznym czarnym wykończeniem i unikalnym systemem uchwytu. Dostępne w wersjach z kolorowymi akcentami.',
                'price' => 56.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 3, // Target
                'image' => 'product04.png',
            ],
            [
                'name' => 'Red Dragon Amberjack 25g',
                'description' => 'Profesjonalne lotki z 90% wolframu z charakterystycznym pomarańczowo-czarnym wzorem grippowym. Wyjątkowa precyzja i kontrola.',
                'price' => 37.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 5, // Red Dragon
                'image' => 'product05.png',
            ],
            [
                'name' => 'Winmau Blackout',
                'description' => 'Eleganckie czarne lotki z 90% wolframu z matowym wykończeniem i precyzyjnym systemem gripowym.',
                'price' => 49.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 4, // Winmau
                'image' => 'product06.png',
            ],
            
            // Lotki Soft
            [
                'name' => 'Target Bolide 01',
                'description' => 'Lotki softip z 90% wolframu zaprojektowane dla maksymalnej kontroli i precyzji.',
                'price' => 59.95,
                'category_id' => 1, // Darts/Lotki 
                'brand_id' => 3, // Target
                'image' => 'product07.png',
            ],
            [
                'name' => 'Harrows Spina',
                'description' => 'Lotki softip z 90% wolframu z charakterystycznym spiralnym wzorem gripowym.',
                'price' => 44.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 2, // Harrows
                'image' => 'product08.png',
            ],
            
            // Akcesoria
            [
                'name' => 'Target Corona Vision Lighting System',
                'description' => 'Profesjonalny system oświetlenia tarczy eliminujący cienie i poprawiający widoczność.',
                'price' => 79.95,
                'category_id' => 3, // Accessories/Akcesoria
                'brand_id' => 3, // Target
                'image' => 'product09.png',
            ],
            [
                'name' => 'Winmau Simon Whitlock Case',
                'description' => 'Profesjonalne etui na lotki z twardą obudową, sygnowane przez Simona Whitlocka.',
                'price' => 19.95,
                'category_id' => 5, // Cases/Etui
                'brand_id' => 4, // Winmau
                'image' => 'shop01.png',
            ],
            
            // Lotki
            [
                'name' => 'Mission Snakebite World Champion Edition',
                'description' => 'Lotki z 90% wolframu sygnowane przez mistrza świata PDC Petera Wrighta, z charakterystycznym wzorem wężowej skóry.',
                'price' => 69.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 7, // Mission
                'image' => 'shop02.png',
            ],
            
            // Shafty
            [
                'name' => 'Target Pro Grip Shafts',
                'description' => 'Profesjonalne shafty z unikalnym systemem Pro Grip zapobiegającym luzowaniu się podczas gry.',
                'price' => 5.95,
                'category_id' => 7, // Shafts/Shafty
                'brand_id' => 3, // Target
                'image' => 'shop03.png',
            ],
            [
                'name' => 'Designa DSX Flights',
                'description' => 'Wytrzymałe i aerodynamiczne lotki dostępne w różnych kształtach i wzorach.',
                'price' => 2.95,
                'category_id' => 6, // Flights/Piórka
                'brand_id' => 9, // Designa
                'image' => 'hotdeal.png',
            ],
            [
                'name' => 'Bull\'s Dartmate Plus Scorepad',
                'description' => 'Profesjonalna tablica do zapisywania wyników z magnetycznym markerem.',
                'price' => 24.95,
                'category_id' => 3, // Accessories/Akcesoria
                'brand_id' => 8, // Bull's
                'image' => 'hotdeal.jpg',
            ],
            [
                'name' => 'One80 Reflex Points',
                'description' => 'Profesjonalne groty steel o zwiększonej wytrzymałości i zmniejszonej częstotliwości odbić.',
                'price' => 9.95,
                'category_id' => 3, // Accessories/Akcesoria
                'brand_id' => 10, // One80
                'image' => 'logo.png',
            ],
            
            // Dodatkowe produkty dla wzbogacenia bazy
            [
                'name' => 'Target Swiss Point Silver',
                'description' => 'Lotki z 90% wolframu z systemem Swiss Point umożliwiającym łatwą wymianę grotów.',
                'price' => 74.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 3, // Target
                'image' => 'product01.png',
            ],
            [
                'name' => 'Harrows Assassin 85%',
                'description' => 'Klasyczne lotki z 85% wolframu o sprawdzonej geometrii i doskonałej równowadze.',
                'price' => 32.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 2, // Harrows
                'image' => 'product02.png',
            ],
            [
                'name' => 'Unicorn Core XL Trainer',
                'description' => 'Tarcza treningowa z pogrubionymi drutami idealna dla początkujących graczy.',
                'price' => 34.95,
                'category_id' => 2, // Dartboards/Tarcze
                'brand_id' => 1, // Unicorn
                'image' => 'product03.png',
            ],
            [
                'name' => 'Red Dragon Championship Shirt',
                'description' => 'Oficjalna koszulka turniejowa z oddychającego materiału.',
                'price' => 29.95,
                'category_id' => 4, // Odzież
                'brand_id' => 5, // Red Dragon
                'image' => 'product04.png',
            ],
            [
                'name' => 'Winmau Xtreme Cabinet Oak',
                'description' => 'Ekskluzywna szafka dębowa z oświetleniem LED i miejscem na akcesoria.',
                'price' => 199.95,
                'category_id' => 7, // Szafy i Szafki
                'brand_id' => 4, // Winmau
                'image' => 'product05.png',
            ],
            [
                'name' => 'Target Takoma Wallet',
                'description' => 'Kompaktowe etui na lotki z miejscem na 2 komplety i akcesoria.',
                'price' => 14.95,
                'category_id' => 5, // Walizki i Etui
                'brand_id' => 3, // Target
                'image' => 'product06.png',
            ],
            [
                'name' => 'Harrows Quantum Soft Tips',
                'description' => 'Końcówki soft tip z polimeru najwyższej jakości o zwiększonej trwałości.',
                'price' => 3.95,
                'category_id' => 6, // Lotki Końcówki
                'brand_id' => 2, // Harrows
                'image' => 'product07.png',
            ],
            [
                'name' => 'Mission LED Surround',
                'description' => 'Podświetlenie LED wokół tarczy eliminujące cienie i poprawiający widoczność.',
                'price' => 89.95,
                'category_id' => 8, // Oświetlenie
                'brand_id' => 7, // Mission
                'image' => 'product08.png',
            ],
            [
                'name' => 'Bull\'s Fighting Pig',
                'description' => 'Limitowana edycja lotek z 95% wolframu z charakterystycznym wzorem świni.',
                'price' => 124.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 8, // Bull's
                'image' => 'product09.png',
            ],
            [
                'name' => 'Designa Patriot X UK',
                'description' => 'Patriotyczne lotki z motywami brytyjskimi, 90% wolfram.',
                'price' => 42.95,
                'category_id' => 1, // Darts/Lotki
                'brand_id' => 9, // Designa
                'image' => 'shop01.png',
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 