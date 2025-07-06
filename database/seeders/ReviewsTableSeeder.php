<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and products by category
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('No users available for reviews.');
            return;
        }

        // Get categories
        $lotkiId = Category::where('slug', 'lotki')->first()->id;
        $tarczeId = Category::where('slug', 'tarcze')->first()->id;
        $oponyId = Category::where('slug', 'opony')->first()->id;
        $piorkaId = Category::where('slug', 'piorka')->first()->id;
        $shaftyId = Category::where('slug', 'shafty')->first()->id;
        $oswietlenieId = Category::where('slug', 'oswietlenie')->first()->id;
        
        // Reviews by category
        $reviewsByCategory = [
            // Recenzje dla tarcz
            $tarczeId => [
                [
                    'title' => 'Najlepsza tarcza na rynku!',
                    'content' => 'Kupiłem tę tarczę miesiąc temu i jestem zachwycony. Cienkie druty, praktycznie zero odbić. Jakość wykonania na najwyższym poziomie. Polecam każdemu kto szuka profesjonalnego sprzętu.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Rewelacyjna tarcza dla klubu',
                    'content' => 'Kupiliśmy tę tarczę do naszego klubu dartowego. Po miesiącu intensywnego użytkowania nie widać śladów zużycia. Druty nadal są idealne, kolory wyraźne. Inwestycja na lata!',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Bardzo dobra jakość',
                    'content' => 'Tarcza wykonana z solidnych materiałów, które wytrzymają lata użytkowania. Druty są cienkie i dobrze zamocowane. Polecam dla początkujących i średnio zaawansowanych graczy.',
                    'rating' => 4,
                    'is_approved' => true,
                    'is_featured' => false
                ]
            ],

            // Recenzje dla lotek
            $lotkiId => [
                [
                    'title' => 'Lotki o perfekcyjnej równowadze',
                    'content' => 'Te lotki mają niesamowitą równowagę. 90% wolfram to czuć od razu - stabilny lot i precyzja rzutów. Po przesiadce z tańszych modeli różnica jest kolosalna.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Profesjonalne lotki dla wymagających',
                    'content' => 'Grywam w darta od 15 lat i to są jedne z najlepszych lotek jakie miałem. Wolfram wysokiej jakości, precyzyjne wykonanie. Warto dopłacić za taką jakość.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Świetny grip i kontrola',
                    'content' => 'Grip jest idealny - nie za ostry i nie za śliski. Lotki świetnie leżą w dłoni, a lot jest bardzo stabilny. Polecam szczególnie dla graczy preferujących rzut z rotacją.',
                    'rating' => 4,
                    'is_approved' => true,
                    'is_featured' => false
                ]
            ],

            // Recenzje dla opon (surrounds)
            $oponyId => [
                [
                    'title' => 'Skuteczna ochrona ściany',
                    'content' => 'Opona doskonale chroni ścianę przed uszkodzeniem. Materiał jest trwały i dobrze tłumi uderzenia. Montaż zajął dosłownie 5 minut.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Solidne wykonanie',
                    'content' => 'Opona jest bardzo solidna, dobrze przylega do ściany. Wygląda profesjonalnie i spełnia swoją funkcję. Jedyny minus to dość wysoka cena.',
                    'rating' => 4,
                    'is_approved' => true,
                    'is_featured' => false
                ]
            ],

            // Recenzje dla piórek
            $piorkaId => [
                [
                    'title' => 'Trwałe i stabilne piórka',
                    'content' => 'Używam tych piórek od miesiąca i jestem pod wrażeniem ich trwałości. Lot jest stabilny, a kształt idealnie pasuje do mojego stylu gry.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Świetny stosunek jakości do ceny',
                    'content' => 'Piórka są bardzo dobrze wykonane, nie odkształcają się i nie niszczą tak szybko jak tańsze modele. W zestawie dostajemy 3 komplety, więc cena jest bardzo atrakcyjna.',
                    'rating' => 4,
                    'is_approved' => true,
                    'is_featured' => false
                ]
            ],

            // Recenzje dla shaftów
            $shaftyId => [
                [
                    'title' => 'Innowacyjny system anti-loose',
                    'content' => 'Te shafty mają świetny system zapobiegający luzowaniu się podczas gry. Materiał jest bardzo wytrzymały, a połączenie z piórkami pewne.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Wytrzymałe i lekkie',
                    'content' => 'Shafty są bardzo lekkie, a jednocześnie wytrzymałe. Nie pękają przy upadku lotki i świetnie trzymają piórka. Polecam szczególnie do cięższych lotek.',
                    'rating' => 4,
                    'is_approved' => true,
                    'is_featured' => false
                ]
            ],

            // Recenzje dla oświetlenia
            $oswietlenieId => [
                [
                    'title' => 'Rewelacyjne oświetlenie tarczy',
                    'content' => 'System oświetlenia idealnie eliminuje cienie. Światło jest równomierne i nie męczy wzroku. Montaż był prosty, a efekt przeszedł moje oczekiwania.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => true
                ],
                [
                    'title' => 'Profesjonalne rozwiązanie',
                    'content' => 'Oświetlenie zmienia całkowicie komfort gry. Regulacja jasności to świetna funkcja, szczególnie podczas długich sesji treningowych. Warto zainwestować.',
                    'rating' => 5,
                    'is_approved' => true,
                    'is_featured' => false
                ]
            ]
        ];
        
        // Create reviews for each category
        foreach ($reviewsByCategory as $categoryId => $reviews) {
            // Get products from this category
            $products = Product::where('category_id', $categoryId)->get();
            
            if ($products->isEmpty()) {
                continue;
            }
            
            foreach ($reviews as $reviewData) {
                // Random user and product from the category
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
}
