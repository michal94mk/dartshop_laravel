<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Frontend\UserReviewRequest;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Exception;

class UserReviewController extends BaseApiController
{
    /**
     * Get all reviews for the authenticated user
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function myReviews(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Fetch user reviews');
            $user = Auth::user();
            $reviews = Review::with('product')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->successResponse($reviews, 'User reviews fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching user reviews');
        }
    }
    
    /**
     * Get a specific review for the authenticated user
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        try {
            $this->logApiRequest(request(), "Fetch user review for ID: {$id}");
            $review = Review::with('product')
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();
            return $this->successResponse($review, 'User review fetched successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Review not found');
        }
    }

    /**
     * Get all reviews for a specific product with statistics
     *
     * @param  int  $productId
     * @return JsonResponse
     */
    public function getProductReviews(int $productId): JsonResponse
    {
        try {
            $this->logApiRequest(request(), "Fetch product reviews for product ID: {$productId}");
            $product = Product::findOrFail($productId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Product not found');
        }
        $reviews = Review::with('user')
            ->where('product_id', $productId)
            ->approved()
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'title' => $review->title,
                    'content' => $review->content,
                    'rating' => $review->rating,
                    'is_featured' => $review->is_featured,
                    'created_at' => $review->created_at,
                    'user' => [
                        'id' => $review->user->id,
                        'name' => $review->user->full_name
                    ]
                ];
            });
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $reviews->where('rating', $i)->count();
        }
        $statistics = [
            'reviews_count' => $totalReviews,
            'average_rating' => $averageRating,
            'distribution' => $distribution
        ];
        return $this->successResponse([
            'reviews' => $reviews,
            'statistics' => $statistics
        ], 'Product reviews fetched successfully');
    }

    /**
     * Store a new review for a product
     *
     * @param  UserReviewRequest  $request
     * @param  int  $productId
     * @return JsonResponse
     */
    public function store(UserReviewRequest $request, int $productId): JsonResponse
    {
        try {
            $this->logApiRequest($request, "Store review for product ID: {$productId}");
            $product = Product::findOrFail($productId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Product not found');
        }
        $user = Auth::user();
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'title' => $request->title,
            'content' => $request->content,
            'rating' => $request->rating,
            'is_featured' => false,
            'status' => 'pending',
        ]);
        return $this->createdResponse($review, 'Review submitted successfully');
    }

    /**
     * Update user's review
     *
     * @param  Request  $request
     * @param  int  $reviewId
     * @return JsonResponse
     */
    public function update(Request $request, int $reviewId): JsonResponse
    {
        $user = Auth::user();
        try {
            $this->logApiRequest($request, "Update user review for ID: {$reviewId}");
            $review = Review::where('user_id', $user->id)->where('id', $reviewId)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Review not found');
        }
        $review->update($request->only(['title', 'content', 'rating']));
        return $this->successResponse($review, 'Review updated successfully');
    }

    /**
     * Delete user's review
     *
     * @param  int  $reviewId
     * @return JsonResponse
     */
    public function destroy(int $reviewId): JsonResponse
    {
        $user = Auth::user();
        try {
            $this->logApiRequest(request(), "Delete user review for ID: {$reviewId}");
            $review = Review::where('user_id', $user->id)->where('id', $reviewId)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Review not found');
        }
        $review->delete();
        return $this->noContentResponse();
    }

    /**
     * Check if authenticated user can review a product
     *
     * @param  int  $productId
     * @return JsonResponse
     */
    public function canReview(int $productId): JsonResponse
    {
        $user = Auth::user();
        $hasReviewed = Review::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();
        return $this->successResponse([
            'can_review' => !$hasReviewed
        ], 'Can review status fetched successfully');
    }

    /**
     * Get the latest approved reviews for the homepage
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getLatestReviews(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Fetch latest approved reviews');
            $reviews = Review::with('user', 'product')
                ->approved()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            return $this->successResponse($reviews, 'Latest approved reviews fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching latest approved reviews');
        }
    }
} 