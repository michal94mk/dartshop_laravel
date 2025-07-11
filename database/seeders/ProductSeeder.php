<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get category IDs
        $lotkiId = Category::where('name', 'Lotki')->first()->id;
        $tarczeId = Category::where('name', 'Tarcze')->first()->id;
        $oponyId = Category::where('name', 'Opony')->first()->id;
        $piorkaId = Category::where('name', 'Piórka')->first()->id;
        $shaftyId = Category::where('name', 'Shafty')->first()->id;
        $oswietlenieId = Category::where('name', 'Oświetlenie')->first()->id;

        // Get brand IDs
        $unicornId = Brand::where('name', 'Unicorn')->first()->id;
        $harrowsId = Brand::where('name', 'Harrows')->first()->id;
        $targetId = Brand::where('name', 'Target')->first()->id;
        $winmauId = Brand::where('name', 'Winmau')->first()->id;

        $products = [
            // Tarcze (Dartboards)
            [
                'name' => 'Winmau Blade 6 Professional',
                'description' => 'Profesjonalna tarcza turniejowa Winmau Blade 6. Wyposażona w rewolucyjny system Dual Core dla zwiększonej trwałości i redukcji odbić. Cieńsze druty i zwiększona powierzchnia scoring area zapewniają lepsze wyniki.',
                'price' => 299.99,
                'category_id' => $tarczeId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-tarcza-winmau-blade-6-profesjonalne.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Target Tor Professional',
                'description' => 'Profesjonalna tarcza Target Tor z innowacyjnym systemem mocowania drutów. Wysoka jakość wykonania i precyzja. Idealna zarówno do użytku domowego jak i turniejowego.',
                'price' => 259.99,
                'category_id' => $tarczeId,
                'brand_id' => $targetId,
                'image_url' => 'target-tarcza-target-tor-profesjonalne.webp',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Unicorn Eclipse Ultra Professional',
                'description' => 'Tarcza turniejowa Unicorn Eclipse Ultra z systemem Eclipse HD. Wyraźny kontrast kolorów i zoptymalizowana konstrukcja drutów dla maksymalnej precyzji i trwałości.',
                'price' => 279.99,
                'category_id' => $tarczeId,
                'brand_id' => $unicornId,
                'image_url' => 'unicorn-tarcza-unicorn-eclipse-ultra-profesjonalne.webp',
                'is_featured' => false,
                'is_active' => true,
            ],

            // Opony (Surrounds)
            [
                'name' => 'Target Pro Tour Dartboard Surround Black',
                'description' => 'Profesjonalna opona turniejowa Target Pro Tour w kolorze czarnym. Chroni ścianę przed uszkodzeniem i ułatwia wyciąganie lotek.',
                'price' => 89.99,
                'category_id' => $oponyId,
                'brand_id' => $targetId,
                'image_url' => 'target-target-pro-tour-dartboard-surround-black.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Winmau Surround Pro Line Blade 6',
                'description' => 'Dedykowana opona do tarczy Blade 6. Wykonana z wysokiej jakości materiału, zapewnia ochronę ściany i profesjonalny wygląd.',
                'price' => 79.99,
                'category_id' => $oponyId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-winmau-surround-pro-line-blade-6.webp',
                'is_featured' => false,
                'is_active' => true,
            ],

            // Piórka (Flights)
            [
                'name' => 'Target Crux Pro Ultra No.6',
                'description' => 'Profesjonalne piórka Target Crux z serii Pro Ultra. Zoptymalizowany kształt nr 6 zapewnia stabilny lot i precyzję.',
                'price' => 9.99,
                'category_id' => $piorkaId,
                'brand_id' => $targetId,
                'image_url' => 'target-piorka-target-crux-3-sets-pro-ultra-no6.jpg',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Target Cult Pro Ultra No.6',
                'description' => 'Piórka Target Cult Pro Ultra o klasycznym kształcie nr 6. Wykonane z wytrzymałego materiału dla długotrwałego użytkowania.',
                'price' => 8.99,
                'category_id' => $piorkaId,
                'brand_id' => $targetId,
                'image_url' => 'target-piorka-target-cult-pro-ultra-no6.jpg',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Target Nathan Aspinall Pro Ultra No.6',
                'description' => 'Sygnowane piórka Nathana Aspinalla z serii Pro Ultra. Charakterystyczny design i sprawdzony kształt.',
                'price' => 11.99,
                'category_id' => $piorkaId,
                'brand_id' => $targetId,
                'image_url' => 'target-piorka-target-nathan-aspinall-pro-ultra-no6.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Unicorn Ultrafly James Wade',
                'description' => 'Piórka sygnowane przez Jamesa Wade\'a z serii Ultrafly. Wyjątkowa trwałość i stabilność lotu.',
                'price' => 10.99,
                'category_id' => $piorkaId,
                'brand_id' => $unicornId,
                'image_url' => 'unicorn-piorka-unicorn-ultrafly-james-wade.webp',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Unicorn Ultrafly Gary Anderson',
                'description' => 'Oficjalne piórka Gary\'ego Andersona z serii Ultrafly. Aerodynamiczny kształt i charakterystyczny design.',
                'price' => 10.99,
                'category_id' => $piorkaId,
                'brand_id' => $unicornId,
                'image_url' => 'unicorn-piorka-unicorn-ultrafly-gary-anderson-phas.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Winmau MVG Standard',
                'description' => 'Standardowe piórka Michaela van Gerwena. Klasyczny kształt i charakterystyczne zielone wykończenie.',
                'price' => 9.99,
                'category_id' => $piorkaId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-piorka-winmau-michael-van-gerwen-standard-b.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Winmau Prism Alpha Joe Cullen',
                'description' => 'Piórka Joe Cullena z serii Prism Alpha. Innowacyjny materiał i unikalny design.',
                'price' => 12.99,
                'category_id' => $piorkaId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-piorka-winmau-prims-alpha-joe-cullen.webp',
                'is_featured' => false,
                'is_active' => true,
            ],

            // Shafty
            [
                'name' => 'Winmau Prism Shaft Blue',
                'description' => 'Niebieskie shafty Winmau Prism wykonane z wytrzymałego materiału kompozytowego. Innowacyjny system zapobiegający luzowaniu.',
                'price' => 14.99,
                'category_id' => $shaftyId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-shafty-winmau-prism-shaft-blue.webp',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Winmau Prism Force Red',
                'description' => 'Czerwone shafty Winmau Prism Force. Wzmocniona konstrukcja i system anty-wibracyjny.',
                'price' => 16.99,
                'category_id' => $shaftyId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-shafty-winmau-prism-force-red.webp',
                'is_featured' => true,
                'is_active' => true,
            ],

            // Oświetlenie
            [
                'name' => 'Target Corona Vision',
                'description' => 'Profesjonalne oświetlenie tarczy Target Corona Vision. Eliminuje cienie i zapewnia równomierne oświetlenie całej powierzchni tarczy.',
                'price' => 199.99,
                'category_id' => $oswietlenieId,
                'brand_id' => $targetId,
                'image_url' => 'target-oswietlenie-tarczy-target-corona-vision.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Winmau Plasma',
                'description' => 'System oświetlenia Winmau Plasma z regulacją jasności. Nowoczesny design i równomierne oświetlenie tarczy.',
                'price' => 179.99,
                'category_id' => $oswietlenieId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-oswietlenie-tarczy-winmau-plasma.webp',
                'is_featured' => false,
                'is_active' => true,
            ],

            // Lotki (Darts) - Harrows
            [
                'name' => 'Harrows Fire Inferno',
                'description' => 'Lotki Harrows Fire Inferno wykonane z wysokiej jakości wolframu. Charakterystyczny czerwony design i precyzyjny grip zapewniający doskonałą kontrolę rzutu.',
                'price' => 129.99,
                'category_id' => $lotkiId,
                'brand_id' => $harrowsId,
                'image_url' => 'harrows-lotki-harrows-fire-inferno.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Harrows Noble',
                'description' => 'Eleganckie lotki Harrows Noble z precyzyjnym systemem gripowym. Idealne wyważenie i kontrola dla graczy na każdym poziomie zaawansowania.',
                'price' => 119.99,
                'category_id' => $lotkiId,
                'brand_id' => $harrowsId,
                'image_url' => 'harrows-lotki-harrows-noble.webp',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Harrows Supergrip Ultra',
                'description' => 'Lotki Harrows Supergrip Ultra z zaawansowanym systemem gripowym. Doskonała kontrola i precyzja rzutu.',
                'price' => 109.99,
                'category_id' => $lotkiId,
                'brand_id' => $harrowsId,
                'image_url' => 'harrows-lotki-harrows-supergrip-ultra.webp',
                'is_featured' => false,
                'is_active' => true,
            ],

            // Lotki (Darts) - Target
            [
                'name' => 'Target 975 Ultra Marine Swiss Point',
                'description' => 'Lotki Target 975 Ultra Marine z systemem Swiss Point. Nowoczesny design i doskonałe parametry techniczne.',
                'price' => 159.99,
                'category_id' => $lotkiId,
                'brand_id' => $targetId,
                'image_url' => 'target-lotki-target-975-ultra-marine-02-swiss-poin.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Target Rob Cross G1 Swiss Point',
                'description' => 'Oficjalne lotki Roba Crossa z serii G1 wyposażone w system Swiss Point. Profesjonalny model turniejowy.',
                'price' => 169.99,
                'category_id' => $lotkiId,
                'brand_id' => $targetId,
                'image_url' => 'target-lotki-target-rob-cross-g1-swiss-point.webp',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Target Sebastian Białecki G1 Swiss Point',
                'description' => 'Sygnowane lotki Sebastiana Białeckiego z serii G1 z systemem Swiss Point. Precyzyjnie wykonany model turniejowy.',
                'price' => 169.99,
                'category_id' => $lotkiId,
                'brand_id' => $targetId,
                'image_url' => 'target-lotki-target-sebastian-bialecki-g1-swiss-po.webp',
                'is_featured' => true,
                'is_active' => true,
            ],

            // Lotki (Darts) - Unicorn
            [
                'name' => 'Unicorn Gary Anderson WC Phase 3',
                'description' => 'Lotki turniejowe Gary\'ego Andersona z serii World Champion Phase 3. Najwyższej jakości wolfram i precyzyjne wykonanie.',
                'price' => 149.99,
                'category_id' => $lotkiId,
                'brand_id' => $unicornId,
                'image_url' => 'unicorn-lotki-unicorn-gary-anderson-wc-phase-3.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Unicorn Premier James Wade',
                'description' => 'Lotki James Wade\'a z serii Premier. Klasyczny design i sprawdzona konstrukcja.',
                'price' => 139.99,
                'category_id' => $lotkiId,
                'brand_id' => $unicornId,
                'image_url' => 'unicorn-lotki-unicorn-premier-james-wade.webp',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Unicorn Ross Smith Two Tone',
                'description' => 'Lotki Ross Smith Two Tone. Unikalny dwukolorowy design i profesjonalne parametry.',
                'price' => 144.99,
                'category_id' => $lotkiId,
                'brand_id' => $unicornId,
                'image_url' => 'unicorn-lotki-unicorn-ross-smith-two-tone.jpg',
                'is_featured' => false,
                'is_active' => true,
            ],

            // Lotki (Darts) - Winmau
            [
                'name' => 'Winmau Blackout',
                'description' => 'Lotki Winmau Blackout z charakterystycznym czarnym wykończeniem. Profesjonalny model o wysokich parametrach.',
                'price' => 134.99,
                'category_id' => $lotkiId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-lotki-winmau-blackout-1.jpg',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Winmau Michael van Gerwen Exact',
                'description' => 'Oficjalne lotki MVG z serii Exact. Precyzyjnie wykonany model turniejowy.',
                'price' => 179.99,
                'category_id' => $lotkiId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-lotki-winmau-michael-van-gerwen-exact.webp',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Winmau Sniper V3',
                'description' => 'Lotki Winmau Sniper V3. Trzecia generacja popularnego modelu z udoskonalonym systemem gripowym.',
                'price' => 124.99,
                'category_id' => $lotkiId,
                'brand_id' => $winmauId,
                'image_url' => 'winmau-lotki-winmau-sniper-v3.webp',
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'category_id' => $product['category_id'],
                'brand_id' => $product['brand_id'],
                'image_url' => $product['image_url'],
                'is_featured' => $product['is_featured'],
                'is_active' => $product['is_active']
            ]);
        }
    }
} 