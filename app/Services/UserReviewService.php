<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class UserReviewService
{
    /**
     * Get all reviews for the given user.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection|Review[]
     */
    public function getUserReviews(User $user)
    {
        return Review::with('product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get a specific review for the given user.
     *
     * @param User $user
     * @param int $reviewId
     * @return Review
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserReview(User $user, int $reviewId)
    {
        return Review::with('product')
            ->where('user_id', $user->id)
            ->where('id', $reviewId)
            ->firstOrFail();
    }

    /**
     * Delete a review for the given user.
     *
     * @param User $user
     * @param int $reviewId
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteUserReview(User $user, int $reviewId): void
    {
        $review = Review::where('user_id', $user->id)->where('id', $reviewId)->firstOrFail();
        $review->delete();
    }

    /**
     * Check if the user can review the given product.
     *
     * @param User $user
     * @param int $productId
     * @return bool
     */
    public function canReview(User $user, int $productId): bool
    {
        return !Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get the latest approved reviews for the homepage.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection|Review[]
     */
    public function getLatestApprovedReviews(int $limit = 9)
    {
        return Review::with('user', 'product')
            ->approved()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new review for a product by a user.
     *
     * @param User $user
     * @param int $productId
     * @param array $data
     * @return Review
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function createUserReview($user, int $productId, array $data)
    {
        // Ensure product exists
        $product = \App\Models\Product::findOrFail($productId);
        // Create review
        return \App\Models\Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'title' => $data['title'],
            'content' => $data['content'],
            'rating' => $data['rating'],
            'is_featured' => false,
            'status' => 'pending',
        ]);
    }

    /**
     * Update a user's review.
     *
     * @param User $user
     * @param int $reviewId
     * @param array $data
     * @return Review
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateUserReview($user, int $reviewId, array $data)
    {
        $review = \App\Models\Review::where('user_id', $user->id)->where('id', $reviewId)->firstOrFail();
        $review->update(array_intersect_key($data, array_flip(['title', 'content', 'rating'])));
        return $review;
    }
} 