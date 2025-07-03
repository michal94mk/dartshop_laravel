<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and products
        $users = User::all();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No users or products available for reviews.');
            return;
        }
        
        // Sample reviews
        $reviews = [
            // Dartboard reviews
            [
                'title' => 'Najlepsza tarcza na rynku!',
                'content' => 'Kupiłem tę tarczę miesiąc temu i jestem zachwycony. Cienkie druty, praktycznie zero odbić. Jakość wykonania na najwyższym poziomie. Polecam każdemu kto szuka profesjonalnego sprzętu.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Bardzo dobra jakość',
                'content' => 'Tarcza wykonana z solidnych materiałów, które wytrzymają lata użytkowania. Polecam dla początkujących i średnio zaawansowanych graczy.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Idealny zestaw do nauki',
                'content' => 'Kupiłem ten zestaw, żeby nauczyć dzieci gry w darta. Świetny stosunek jakości do ceny. Polecam wszystkim początkującym!',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            
            // Darts reviews
            [
                'title' => 'Lotki o perfekcyjnej równowadze',
                'content' => 'Te lotki mają niesamowitą równowagę. 90% wolfram to czuć od razu - stabilny lot i precyzja rzutów. Po przesiadce z tańszych modeli różnica jest kolosalna.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Świetny design i funkcjonalność',
                'content' => 'To już mój drugi zakup w tym sklepie i ponownie jestem bardzo zadowolony. Szybka dostawa, produkty zgodne z opisem. DartShop to mój ulubiony sklep z akcesoriami do darta!',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Dobry stosunek jakości do ceny',
                'content' => 'Nie spodziewałem się takiej dobrej jakości w tej cenie. Polecam każdemu kto szuka solidnego sprzętu bez wydawania fortuny.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Profesjonalne lotki dla wymagających',
                'content' => 'Grywam w darta od 15 lat i to są jedne z najlepszych lotek jakie miałem. Wolfram wysokiej jakości, precyzyjne wykonanie. Warto dopłacić za taką jakość.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Mieszane uczucia',
                'content' => 'Lotki są w porządku, ale spodziewałem się więcej za tę cenę. Jakość wykonania dobra, ale nie wybitna. Dla rozpoczynających przygodę z dartem w sam raz.',
                'rating' => 3,
                'is_approved' => true,
                'is_featured' => false
            ],
            
            // Accessories reviews
            [
                'title' => 'Świetne oświetlenie',
                'content' => 'System oświetlenia idealnie eliminuje cienie. Po zamontowaniu gra stała się o wiele przyjemniejsza, szczególnie wieczorami. Montaż prosty i szybki.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Solidne etui',
                'content' => 'Etui chroni lotki perfekcyjnie. Zmieści się w nim wszystko co potrzeba - lotki, groty, shafty. Materiał wygląda na trwały.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Niezawodne groty',
                'content' => 'Groty wbijają się mocno w tarczę i rzadko się łamią. Zdecydowanie lepsze od standardowych grotów w zestawach początkowych.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            
            // More detailed reviews
            [
                'title' => 'Rewelacyjna tarcza dla klubu',
                'content' => 'Kupiliśmy tę tarczę do naszego klubu dartowego. Po miesiącu intensywnego użytkowania nie widać śladów zużycia. Druty nadal są idealne, kolory wyraźne. Inwestycja na lata!',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Genialny zestaw dla początkujących',
                'content' => 'Moja żona zaczęła grać w darta i ten zestaw był idealnym wyborem. Wszystko czego potrzeba w jednym pakiecie. Instrukcja bardzo pomocna.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Stylowe i funkcjonalne',
                'content' => 'Te lotki nie tylko świetnie wyglądają, ale również doskonale się rzuca. Czarno-pomarańczowy design przyciąga wzrok na turnieju.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Luksusowa szafka, ale cena...',
                'content' => 'Szafka wykonana bardzo solidnie, dęb wygląda przepięknie. Oświetlenie LED dodaje stylu. Jedyny minus to cena - dla niektórych może być zbyt wysoka.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Kompaktowe i praktyczne',
                'content' => 'Małe etui które zmieści się w kieszeni. Świetne na podróże i wyjazdy turniejowe. Polecam każdemu kto często gra poza domem.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Końcówki soft tip najwyższej jakości',
                'content' => 'Te końcówki są znacznie trwalsze od standardowych. Wytrzymują setki rzutów bez uszkodzeń. Definitely worth the money!',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Podświetlenie które zmienia wszystko',
                'content' => 'Zamontowałem to podświetlenie LED i różnica jest ogromna. Żadnych cieni, równomierne oświetlenie całej tarczy. Gra stała się o wiele precyzyjniejsza.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Limitowana edycja na medal',
                'content' => 'Lotki limitowane Fighting Pig to prawdziwa perła. 95% wolfram, niesamowita precyzja, unikalna stylistyka. Bardzo trudno dostępne - cieszę się że udało mi się je kupić.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Patriotyczne lotki z charakterem',
                'content' => 'Lotki z motywami brytyjskimi wyglądają świetnie. Jakość wykonania dobra, ale nic wybitnego. Kupowałem głównie ze względu na design.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Koszulka dla prawdziwych fanów',
                'content' => 'Oficjalna koszulka turniejowa bardzo dobrej jakości. Materiał oddychający, doskonały krój. Czuję się jak zawodowiec na turnieju!',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ]
        ];
        
        foreach ($reviews as $reviewData) {
            // Random user and product for each review
            $user = $users->random();
            $product = $products->random();
            
            // Check if user already reviewed this product
            $existingReview = Review::where('user_id', $user->id)
                                  ->where('product_id', $product->id)
                                  ->first();
            
            // Create review if doesn't exist
            if (!$existingReview) {
                Review::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'title' => $reviewData['title'],
                    'content' => $reviewData['content'],
                    'rating' => $reviewData['rating'],
                    'is_approved' => $reviewData['is_approved'],
                    'is_featured' => $reviewData['is_featured']
                ]);
            }
        }
    }
}
