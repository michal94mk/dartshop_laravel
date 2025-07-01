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
            [
                'title' => 'Best darts on the market!',
                'content' => 'Bought these darts a month ago and I\'m thrilled. Stable flight and top-quality construction. Recommend to anyone looking for professional equipment.',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Very good quality',
                'content' => 'Dartboard made from solid materials that will last for years. Recommend for beginners and intermediate players.',
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Perfect learning set',
                'content' => 'Bought this set to teach my kids darts. Great value for money. Recommend to all beginners!',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true
            ],
            [
                'title' => 'Great design and functionality',
                'content' => 'This is my second purchase from this store and again I\'m very satisfied. Fast delivery, products as described. DartShop is my favorite dart accessories store!',
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => false
            ],
            [
                'title' => 'Good value for money',
                'content' => 'Didn\'t expect such good quality at this price. Recommend to anyone looking for solid equipment without spending a fortune.',
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
                    'comment' => $reviewData['content'],
                    'rating' => $reviewData['rating'],
                    'is_approved' => $reviewData['is_approved']
                ]);
            }
        }
    }
}
