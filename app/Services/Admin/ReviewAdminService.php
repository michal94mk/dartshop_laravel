<?php

namespace App\Services\Admin;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service class for admin review management.
 * Handles business logic for listing, creating, updating, deleting, approving, rejecting, and featuring reviews.
 */
class ReviewAdminService
{
    /**
     * Get paginated reviews with optional filters and sorting.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getReviewsWithFilters(Request $request): LengthAwarePaginator
    {
        $query = Review::with(['product', 'user']);
        // Filtering
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->has('rating') && !empty($request->rating)) {
            $query->where('rating', $request->rating);
        }
        if ($request->has('approved') && $request->approved !== null && $request->approved !== '') {
            $query->where('is_approved', $request->approved == 'true' || $request->approved == 1);
        }
        if ($request->has('featured') && $request->featured !== null && $request->featured !== '') {
            $query->where('is_featured', $request->featured == 'true' || $request->featured == 1);
        }
        // Sorting
        $sortField = $request->sort_field ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        if ($sortField === 'product') {
            $query->join('products', 'reviews.product_id', '=', 'products.id')
                  ->orderBy('products.name', $sortDirection)
                  ->select('reviews.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }
        // Pagination
        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }

    /**
     * Create a new review. Throws exception if user already reviewed the product or featured limit is reached.
     *
     * @param array $data
     * @return Review
     * @throws \Exception
     */
    public function createReview(array $data): Review
    {
        // Check featured limit if review is to be featured and approved
        if (($data['is_featured'] ?? false) && ($data['is_approved'] ?? true)) {
            $featuredCount = Review::approvedAndFeatured()->count();
            if ($featuredCount >= 6) {
                throw new \Exception('You can feature up to 6 reviews. Remove feature from another review to add a new one.');
            }
        }
        // Ensure user can only review a product once
        if (Review::where('user_id', $data['user_id'])->where('product_id', $data['product_id'])->exists()) {
            throw new \Exception('This user has already reviewed this product.');
        }
        return Review::create($data);
    }

    /**
     * Update an existing review. Throws exception if constraints are violated.
     *
     * @param Review $review
     * @param array $data
     * @return Review
     * @throws \Exception
     */
    public function updateReview(Review $review, array $data): Review
    {
        // Check featured limit if review is to be featured and approved
        if (($data['is_featured'] ?? false) && ($data['is_approved'] ?? true)) {
            $featuredCount = Review::approvedAndFeatured()->where('id', '!=', $review->id)->count();
            if ($featuredCount >= 6) {
                throw new \Exception('You can feature up to 6 reviews. Remove feature from another review to add a new one.');
            }
        }
        // Ensure user can only review a product once
        if ((isset($data['user_id']) && $data['user_id'] != $review->user_id) || (isset($data['product_id']) && $data['product_id'] != $review->product_id)) {
            if (Review::where('user_id', $data['user_id'] ?? $review->user_id)
                ->where('product_id', $data['product_id'] ?? $review->product_id)
                ->where('id', '!=', $review->id)
                ->exists()) {
                throw new \Exception('This user has already reviewed this product.');
            }
        }
        $review->update($data);
        return $review->fresh(['product', 'user']);
    }

    /**
     * Delete a review.
     *
     * @param Review $review
     * @return void
     */
    public function deleteReview(Review $review): void
    {
        $review->delete();
    }

    /**
     * Approve a review. Throws exception if featured limit is reached.
     *
     * @param Review $review
     * @return Review
     * @throws \Exception
     */
    public function approveReview(Review $review): Review
    {
        // If review is featured, check featured limit
        if ($review->is_featured) {
            $featuredCount = Review::approvedAndFeatured()->where('id', '!=', $review->id)->count();
            if ($featuredCount >= 6) {
                throw new \Exception('You can feature up to 6 reviews. Remove feature from another review to approve this one.');
            }
        }
        $review->is_approved = true;
        $review->save();
        return $review->fresh(['product', 'user']);
    }

    /**
     * Reject a review. If featured, removes featured status.
     *
     * @param Review $review
     * @return Review
     */
    public function rejectReview(Review $review): Review
    {
        $review->is_approved = false;
        // Remove featured status if review is rejected
        if ($review->is_featured) {
            $review->is_featured = false;
        }
        $review->save();
        return $review->fresh(['product', 'user']);
    }

    /**
     * Toggle the featured status of a review. Throws exception if constraints are violated.
     *
     * @param Review $review
     * @return Review
     * @throws \Exception
     */
    public function toggleFeatured(Review $review): Review
    {
        if (!$review->is_featured) {
            // To feature, review must be approved and featured limit not exceeded
            if (!$review->is_approved) {
                throw new \Exception('Review must be approved to be featured.');
            }
            $featuredCount = Review::approvedAndFeatured()->where('id', '!=', $review->id)->count();
            if ($featuredCount >= 6) {
                throw new \Exception('You can feature up to 6 reviews. Remove feature from another review to add a new one.');
            }
            $review->is_featured = true;
        } else {
            $review->is_featured = false;
        }
        $review->save();
        return $review->fresh(['product', 'user']);
    }

    /**
     * Get data for review form (users and products).
     *
     * @return array{products: \Illuminate\Support\Collection, users: \Illuminate\Support\Collection}
     */
    public function getFormData(): array
    {
        $products = Product::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        return [
            'products' => $products,
            'users' => $users
        ];
    }

    /**
     * Get the count of approved and featured reviews.
     *
     * @return int
     */
    public function getFeaturedCount(): int
    {
        return Review::approvedAndFeatured()->count();
    }
}
