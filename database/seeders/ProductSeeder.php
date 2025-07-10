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
        $lotkiId = Category::where('slug', 'lotki')->first()->id;
        $tarczeId = Category::where('slug', 'tarcze')->first()->id;
        $oponyId = Category::where('slug', 'opony')->first()->id;
        $piorkaId = Category::where('slug', 'piorka')->first()->id;
        $shaftyId = Category::where('slug', 'shafty')->first()->id;
        $oswietlenieId = Category::where('slug', 'oswietlenie')->first()->id;

        // Get brand IDs
        $unicornId = Brand::where('slug', 'unicorn')->first()->id;
        $harrowsId = Brand::where('slug', 'harrows')->first()->id;
        $targetId = Brand::where('slug', 'target')->first()->id;
        $winmauId = Brand::where('slug', 'winmau')->first()->id;

        $products = [
            // Tarcze (Dartboards)
            [
                'name' => 'Winmau Blade 6 Professional',
                'description' => 'Profesjonalna tarcza turniejowa Winmau Blade 6. Wyposażona w rewolucyjny system Dual Core dla zwiększonej trwałości i redukcji odbić. Cieńsze druty i zwiększona powierzchnia scoring area zapewniają lepsze wyniki.',
                'price' => 299.99,
                'category_id' => $tarczeId,
                'brand_id' => $winmauId,
                'image' => 'winmau-tarcza-winmau-blade-6-profesjonalne.webp',
                'featured' => true,
            ],
            [
                'name' => 'Target Tor Professional',
                'description' => 'Profesjonalna tarcza Target Tor z innowacyjnym systemem mocowania drutów. Wysoka jakość wykonania i precyzja. Idealna zarówno do użytku domowego jak i turniejowego.',
                'price' => 259.99,
                'category_id' => $tarczeId,
                'brand_id' => $targetId,
                'image' => 'target-tarcza-target-tor-profesjonalne.webp',
                'featured' => false,
            ],
            [
                'name' => 'Unicorn Eclipse Ultra Professional',
                'description' => 'Tarcza turniejowa Unicorn Eclipse Ultra z systemem Eclipse HD. Wyraźny kontrast kolorów i zoptymalizowana konstrukcja drutów dla maksymalnej precyzji i trwałości.',
                'price' => 279.99,
                'category_id' => $tarczeId,
                'brand_id' => $unicornId,
                'image' => 'unicorn-tarcza-unicorn-eclipse-ultra-profesjonalne.webp',
                'featured' => false,
            ],

            // Opony (Surrounds)
            [
                'name' => 'Target Pro Tour Dartboard Surround Black',
                'description' => 'Profesjonalna opona turniejowa Target Pro Tour w kolorze czarnym. Chroni ścianę przed uszkodzeniem i ułatwia wyciąganie lotek.',
                'price' => 89.99,
                'category_id' => $oponyId,
                'brand_id' => $targetId,
                'image' => 'target-target-pro-tour-dartboard-surround-black.webp',
                'featured' => true,
            ],
            [
                'name' => 'Winmau Surround Pro Line Blade 6',
                'description' => 'Dedykowana opona do tarczy Blade 6. Wykonana z wysokiej jakości materiału, zapewnia ochronę ściany i profesjonalny wygląd.',
                'price' => 79.99,
                'category_id' => $oponyId,
                'brand_id' => $winmauId,
                'image' => 'winmau-winmau-surround-pro-line-blade-6.webp',
                'featured' => false,
            ],

            // Piórka (Flights)
            [
                'name' => 'Target Crux Pro Ultra No.6',
                'description' => 'Profesjonalne piórka Target Crux z serii Pro Ultra. Zoptymalizowany kształt nr 6 zapewnia stabilny lot i precyzję.',
                'price' => 9.99,
                'category_id' => $piorkaId,
                'brand_id' => $targetId,
                'image' => 'target-piorka-target-crux-3-sets-pro-ultra-no6.jpg',
                'featured' => false,
            ],
            [
                'name' => 'Target Cult Pro Ultra No.6',
                'description' => 'Piórka Target Cult Pro Ultra o klasycznym kształcie nr 6. Wykonane z wytrzymałego materiału dla długotrwałego użytkowania.',
                'price' => 8.99,
                'category_id' => $piorkaId,
                'brand_id' => $targetId,
                'image' => 'target-piorka-target-cult-pro-ultra-no6.jpg',
                'featured' => false,
            ],
            [
                'name' => 'Target Nathan Aspinall Pro Ultra No.6',
                'description' => 'Sygnowane piórka Nathana Aspinalla z serii Pro Ultra. Charakterystyczny design i sprawdzony kształt.',
                'price' => 11.99,
                'category_id' => $piorkaId,
                'brand_id' => $targetId,
                'image' => 'target-piorka-target-nathan-aspinall-pro-ultra-no6.webp',
                'featured' => true,
            ],
            [
                'name' => 'Unicorn Ultrafly James Wade',
                'description' => 'Piórka sygnowane przez Jamesa Wade\'a z serii Ultrafly. Wyjątkowa trwałość i stabilność lotu.',
                'price' => 10.99,
                'category_id' => $piorkaId,
                'brand_id' => $unicornId,
                'image' => 'unicorn-piorka-unicorn-ultrafly-james-wade.webp',
                'featured' => false,
            ],
            [
                'name' => 'Unicorn Ultrafly Gary Anderson',
                'description' => 'Oficjalne piórka Gary\'ego Andersona z serii Ultrafly. Aerodynamiczny kształt i charakterystyczny design.',
                'price' => 10.99,
                'category_id' => $piorkaId,
                'brand_id' => $unicornId,
                'image' => 'unicorn-piorka-unicorn-ultrafly-gary-anderson-phas.webp',
                'featured' => true,
            ],
            [
                'name' => 'Winmau MVG Standard',
                'description' => 'Standardowe piórka Michaela van Gerwena. Klasyczny kształt i charakterystyczne zielone wykończenie.',
                'price' => 9.99,
                'category_id' => $piorkaId,
                'brand_id' => $winmauId,
                'image' => 'winmau-piorka-winmau-michael-van-gerwen-standard-b.webp',
                'featured' => true,
            ],
            [
                'name' => 'Winmau Prism Alpha Joe Cullen',
                'description' => 'Piórka Joe Cullena z serii Prism Alpha. Innowacyjny materiał i unikalny design.',
                'price' => 12.99,
                'category_id' => $piorkaId,
                'brand_id' => $winmauId,
                'image' => 'winmau-piorka-winmau-prims-alpha-joe-cullen.webp',
                'featured' => false,
            ],

            // Shafty
            [
                'name' => 'Winmau Prism Shaft Blue',
                'description' => 'Niebieskie shafty Winmau Prism wykonane z wytrzymałego materiału kompozytowego. Innowacyjny system zapobiegający luzowaniu.',
                'price' => 14.99,
                'category_id' => $shaftyId,
                'brand_id' => $winmauId,
                'image' => 'winmau-shafty-winmau-prism-shaft-blue.webp',
                'featured' => false,
            ],
            [
                'name' => 'Winmau Prism Force Red',
                'description' => 'Czerwone shafty Winmau Prism Force. Wzmocniona konstrukcja i system anty-wibracyjny.',
                'price' => 16.99,
                'category_id' => $shaftyId,
                'brand_id' => $winmauId,
                'image' => 'winmau-shafty-winmau-prism-force-red.webp',
                'featured' => true,
            ],

            // Oświetlenie
            [
                'name' => 'Target Corona Vision',
                'description' => 'Profesjonalne oświetlenie tarczy Target Corona Vision. Eliminuje cienie i zapewnia równomierne oświetlenie całej powierzchni tarczy.',
                'price' => 199.99,
                'category_id' => $oswietlenieId,
                'brand_id' => $targetId,
                'image' => 'target-oswietlenie-tarczy-target-corona-vision.webp',
                'featured' => true,
            ],
            [
                'name' => 'Winmau Plasma',
                'description' => 'System oświetlenia Winmau Plasma z regulacją jasności. Nowoczesny design i równomierne oświetlenie tarczy.',
                'price' => 179.99,
                'category_id' => $oswietlenieId,
                'brand_id' => $winmauId,
                'image' => 'winmau-oswietlenie-tarczy-winmau-plasma.webp',
                'featured' => false,
            ],

            // Lotki (Darts) - Harrows
            [
                'name' => 'Harrows Fire Inferno',
                'description' => 'Lotki Harrows Fire Inferno wykonane z wysokiej jakości wolframu. Charakterystyczny czerwony design i precyzyjny grip zapewniający doskonałą kontrolę rzutu.',
                'price' => 129.99,
                'category_id' => $lotkiId,
                'brand_id' => $harrowsId,
                'image' => 'harrows-lotki-harrows-fire-inferno.webp',
                'featured' => true,
            ],
            [
                'name' => 'Harrows Noble',
                'description' => 'Eleganckie lotki Harrows Noble z precyzyjnym systemem gripowym. Idealne wyważenie i kontrola dla graczy na każdym poziomie zaawansowania.',
                'price' => 119.99,
                'category_id' => $lotkiId,
                'brand_id' => $harrowsId,
                'image' => 'harrows-lotki-harrows-noble.webp',
                'featured' => false,
            ],
            [
                'name' => 'Harrows Supergrip Ultra',
                'description' => 'Lotki Harrows Supergrip Ultra z zaawansowanym systemem uchwytu. Specjalna powierzchnia gripowa zapewnia pewny chwyt w każdych warunkach.',
                'price' => 109.99,
                'category_id' => $lotkiId,
                'brand_id' => $harrowsId,
                'image' => 'harrows-lotki-harrows-supergrip-ultra.webp',
                'featured' => false,
            ],

            // Lotki (Darts) - Target
            [
                'name' => 'Target 975 Ultra Marine Swiss Point',
                'description' => 'Profesjonalne lotki Target 975 Ultra Marine z systemem Swiss Point. Wykonane z wysokiej jakości wolframu, zapewniają doskonałą precyzję i możliwość szybkiej wymiany grotów.',
                'price' => 189.99,
                'category_id' => $lotkiId,
                'brand_id' => $targetId,
                'image' => 'target-lotki-target-975-ultra-marine-02-swiss-poin.webp',
                'featured' => true,
            ],
            [
                'name' => 'Target Rob Cross G1 Swiss Point',
                'description' => 'Sygnowane lotki mistrza świata PDC Roba Crossa. Wyposażone w system Swiss Point, wykonane z najwyższej jakości wolframu z unikalnym designem.',
                'price' => 199.99,
                'category_id' => $lotkiId,
                'brand_id' => $targetId,
                'image' => 'target-lotki-target-rob-cross-g1-swiss-point.webp',
                'featured' => true,
            ],
            [
                'name' => 'Target Sebastian Bialecki G1 Swiss Point',
                'description' => 'Oficjalne lotki polskiego talentu Sebastiana Białeckiego. Wykonane z premium wolframu, wyposażone w system Swiss Point i charakterystyczny design.',
                'price' => 179.99,
                'category_id' => $lotkiId,
                'brand_id' => $targetId,
                'image' => 'target-lotki-target-sebastian-bialecki-g1-swiss-po.webp',
                'featured' => true,
            ],

            // Lotki (Darts) - Unicorn
            [
                'name' => 'Unicorn Gary Anderson WC Phase 3',
                'description' => 'Lotki dwukrotnego mistrza świata Gary\'ego Andersona. Najnowsza seria Phase 3 z unikalnym systemem gripowym i premium wykończeniem.',
                'price' => 189.99,
                'category_id' => $lotkiId,
                'brand_id' => $unicornId,
                'image' => 'unicorn-lotki-unicorn-gary-anderson-wc-phase-3.webp',
                'featured' => true,
            ],
            [
                'name' => 'Unicorn Premier James Wade',
                'description' => 'Sygnowane lotki Jamesa Wade\'a z serii Premier. Precyzyjnie wykonane z wysokiej jakości wolframu, zapewniające doskonałą kontrolę rzutu.',
                'price' => 159.99,
                'category_id' => $lotkiId,
                'brand_id' => $unicornId,
                'image' => 'unicorn-lotki-unicorn-premier-james-wade.webp',
                'featured' => false,
            ],
            [
                'name' => 'Unicorn Ross Smith Two Tone',
                'description' => 'Profesjonalne lotki Rossa Smitha w unikalnym dwukolorowym wykończeniu. Zaawansowany system gripowy i precyzyjne wyważenie.',
                'price' => 149.99,
                'category_id' => $lotkiId,
                'brand_id' => $unicornId,
                'image' => 'unicorn-lotki-unicorn-ross-smith-two-tone.jpg',
                'featured' => false,
            ],

            // Lotki (Darts) - Winmau
            [
                'name' => 'Winmau Blackout',
                'description' => 'Lotki Winmau Blackout z charakterystycznym czarnym wykończeniem. Wykonane z wysokiej jakości wolframu, zapewniają doskonałą kontrolę i precyzję rzutu.',
                'price' => 139.99,
                'category_id' => $lotkiId,
                'brand_id' => $winmauId,
                'image' => 'winmau-lotki-winmau-blackout-1.jpg',
                'featured' => false,
            ],
            [
                'name' => 'Winmau Michael van Gerwen Exact',
                'description' => 'Oficjalne lotki trzykrotnego mistrza świata Michaela van Gerwena. Precyzyjnie wykonane według specyfikacji MVG, zapewniające najwyższą jakość gry.',
                'price' => 199.99,
                'category_id' => $lotkiId,
                'brand_id' => $winmauId,
                'image' => 'winmau-lotki-winmau-michael-van-gerwen-exact.webp',
                'featured' => true,
            ],
            [
                'name' => 'Winmau Sniper V3',
                'description' => 'Trzecia generacja popularnych lotek Winmau Sniper. Ulepszona konstrukcja z precyzyjnym systemem gripowym i doskonałym wyważeniem.',
                'price' => 129.99,
                'category_id' => $lotkiId,
                'brand_id' => $winmauId,
                'image' => 'winmau-lotki-winmau-sniper-v3.webp',
                'featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }
    }
} 