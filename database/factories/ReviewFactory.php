<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->paragraph(3),
            'is_approved' => $this->faker->boolean(70),
            'is_featured' => $this->faker->boolean(10),
        ];
    }

    /**
     * Configure the factory to ensure unique user-product combinations.
     */
    public function configure()
    {
        return $this->afterMaking(function ($review) {
            // Ensure unique combination
            $existingReview = \App\Models\Review::where('user_id', $review->user_id)
                ->where('product_id', $review->product_id)
                ->first();
            
            if ($existingReview) {
                $review->user_id = User::factory()->create()->id;
            }
        });
    }
} 