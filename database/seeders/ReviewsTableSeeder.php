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
        // Pobieramy istniejących użytkowników i produkty
        $users = User::all();
        $products = Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('Brak użytkowników lub produktów do przypisania recenzji.');
            return;
        }
        
        // Przykładowe recenzje
        $reviews = [
            [
                'title' => 'Najlepsze lotki na rynku!',
                'content' => 'Kupiłem te lotki miesiąc temu i jestem zachwycony. Lot jest stabilny, a wykonanie na najwyższym poziomie. Polecam każdemu, kto szuka profesjonalnego sprzętu.',
                'rating' => 5,
                'is_approved' => true
            ],
            [
                'title' => 'Bardzo dobra jakość',
                'content' => 'Tarcza jest wykonana z solidnych materiałów, które wytrzymają lata użytkowania. Polecam początkującym i średniozaawansowanym graczom.',
                'rating' => 4,
                'is_approved' => true
            ],
            [
                'title' => 'Idealny zestaw do nauki',
                'content' => 'Kupiłem ten zestaw, aby nauczyć moje dzieci gry w darta. Świetny stosunek jakości do ceny. Polecam wszystkim początkującym!',
                'rating' => 5,
                'is_approved' => true
            ],
            [
                'title' => 'Świetny design i funkcjonalność',
                'content' => 'To już mój drugi zakup w tym sklepie i ponownie jestem bardzo zadowolony. Szybka dostawa, produkty zgodne z opisem. DartShop to mój ulubiony sklep z akcesoriami do darta!',
                'rating' => 5,
                'is_approved' => true
            ],
            [
                'title' => 'Dobry stosunek jakości do ceny',
                'content' => 'Nie spodziewałem się tak dobrej jakości w tej cenie. Polecam każdemu, kto szuka solidnego sprzętu bez wydawania fortuny.',
                'rating' => 4,
                'is_approved' => true
            ]
        ];
        
        foreach ($reviews as $reviewData) {
            // Losowy użytkownik i produkt dla każdej recenzji
            $user = $users->random();
            $product = $products->random();
            
            // Spradzamy czy ten użytkownik już dodał recenzję do tego produktu
            $existingReview = Review::where('user_id', $user->id)
                                  ->where('product_id', $product->id)
                                  ->first();
            
            // Jeśli nie, tworzymy nową recenzję
            if (!$existingReview) {
                Review::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'title' => $reviewData['title'],
                    'content' => $reviewData['content'],
                    'rating' => $reviewData['rating'],
                    'is_approved' => $reviewData['is_approved']
                ]);
            }
        }
    }
}
