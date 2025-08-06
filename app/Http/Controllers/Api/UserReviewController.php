<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Frontend\UserReviewRequest;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Services\UserReviewService;

class UserReviewController extends BaseApiController
{
    protected $userReviewService;

    public function __construct(UserReviewService $userReviewService)
    {
        $this->userReviewService = $userReviewService;
    }
    /**
     * Get all reviews for the authenticated user
     */
    public function myReviews(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch user reviews');
        $user = Auth::user();
        $reviews = $this->userReviewService->getUserReviews($user);
        return $this->successResponse($reviews, 'User reviews fetched successfully');
    }
    /**
     * Get a specific review for the authenticated user
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        $this->logApiRequest(request(), "Fetch user review for ID: {$id}");
        $review = $this->userReviewService->getUserReview($user, $id);
        return $this->successResponse($review, 'User review fetched successfully');
    }

    /**
     * Get all reviews for a specific product with statistics
     *
     * @param  int  $productId
     * @return JsonResponse
     */
    public function getProductReviews(int $productId): JsonResponse
    {
        $this->logApiRequest(request(), "Fetch product reviews for product ID: {$productId}");
        $product = Product::findOrFail($productId);
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
     */
    public function store(UserReviewRequest $request, int $productId): JsonResponse
    {
        $user = Auth::user();
        // Review creation logic is handled in the service
        $review = $this->userReviewService->createUserReview($user, $productId, $request->all());
        return $this->createdResponse($review, 'Review submitted successfully');
    }

    /**
     * Update user's review
     */
    public function update(UserReviewRequest $request, int $reviewId): JsonResponse
    {
        $user = Auth::user();
        // Review update logic is handled in the service
        $review = $this->userReviewService->updateUserReview($user, $reviewId, $request->validated());
        return $this->successResponse($review, 'Review updated successfully');
    }

    /**
     * Delete user's review
     */
    public function destroy(int $reviewId): JsonResponse
    {
        $user = Auth::user();
        $this->logApiRequest(request(), "Delete user review for ID: {$reviewId}");
        $this->userReviewService->deleteUserReview($user, $reviewId);
        return $this->noContentResponse();
    }

    /**
     * Check if authenticated user can review a product
     */
    public function canReview(int $productId): JsonResponse
    {
        $user = Auth::user();
        $canReview = $this->userReviewService->canReview($user, $productId);
        return $this->successResponse([
            'can_review' => $canReview
        ], 'Can review status fetched successfully');
    }

    /**
     * Get the latest approved reviews for the homepage
     */
    public function getLatestReviews(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch latest approved reviews');
        $reviews = $this->userReviewService->getLatestApprovedReviews();
        return $this->successResponse($reviews, 'Latest approved reviews fetched successfully');
    }
} 