<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\ReviewAdminService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\ReviewRequest;
use App\Models\Review;


/**
 * API controller for admin review management.
 * Handles requests for listing, creating, updating, deleting, approving, rejecting, and featuring reviews.
 */
class ReviewController extends BaseApiController
{
    public function __construct(
        private ReviewAdminService $reviewAdminService
    ) {}

    /**
     * Get a paginated list of reviews with filters and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $reviews = $this->reviewAdminService->getReviewsWithFilters($request);
        return $this->paginatedResponse($reviews);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(ReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Check featured limit if trying to create a featured review
        if (isset($validated['is_featured']) && $validated['is_featured']) {
            // Check if review is approved
            if (!($validated['is_approved'] ?? true)) {
                return $this->errorResponse('Recenzja musi być zatwierdzona, aby ją wyróżnić.', 422);
            }
            
            $featuredCount = Review::approvedAndFeatured()->count();
            if ($featuredCount >= 6) {
                return $this->errorResponse('Możesz wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji, aby dodać nową.', 422);
            }
        }
        
        $review = $this->reviewAdminService->createReview($validated);
        return $this->successResponse($review, 'Recenzja została utworzona', 201);
    }

    /**
     * Display the specified review.
     */
    public function show(int $id): JsonResponse
    {
        $review = $this->reviewAdminService->getReviewWithDetails($id);
        return $this->successResponse($review, 'Recenzja pobrana');
    }

    /**
     * Update the specified review in storage.
     */
    public function update(ReviewRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        // Check featured limit if trying to make a review featured
        if (isset($validated['is_featured']) && $validated['is_featured']) {
            $review = Review::findOrFail($id);
            
            // Check if review is approved
            if (!($validated['is_approved'] ?? $review->is_approved)) {
                return $this->errorResponse('Recenzja musi być zatwierdzona, aby ją wyróżnić.', 422);
            }
            
            $featuredCount = Review::approvedAndFeatured()->count();
            if ($featuredCount >= 6) {
                return $this->errorResponse('Możesz wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji, aby dodać nową.', 422);
            }
        }
        
        $review = $this->reviewAdminService->updateReview($id, $validated);
        return $this->successResponse($review, 'Recenzja została zaktualizowana');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->reviewAdminService->deleteReview($id);
        return $this->successResponse('Recenzja została usunięta');
    }

    /**
     * Approve a review.
     */
    public function approve(int $id): JsonResponse
    {
        $review = $this->reviewAdminService->approveReview($id);
        return $this->successResponse($review, 'Recenzja została zatwierdzona');
    }

    /**
     * Reject a review.
     */
    public function reject(int $id): JsonResponse
    {
        $review = $this->reviewAdminService->rejectReview($id);
        return $this->successResponse($review, 'Recenzja została odrzucona');
    }

    /**
     * Toggle featured status of a review.
     */
    public function toggleFeatured(int $id): JsonResponse
    {
        try {
            $review = $this->reviewAdminService->toggleFeaturedReview($id);
            
            $message = $review->is_featured 
                ? 'Recenzja została wyróżniona.' 
                : 'Recenzja została usunięta z wyróżnienia.';
                
            return $this->successResponse(['review' => $review], $message);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Get form data for creating/editing reviews.
     */
    public function getFormData(): JsonResponse
    {
        $formData = $this->reviewAdminService->getFormData();
        return $this->successResponse($formData, 'Form data fetched successfully');
    }

    /**
     * Get featured reviews count.
     */
    public function getFeaturedCount(): JsonResponse
    {
        $featuredCount = Review::approvedAndFeatured()->count();
        
        return $this->successResponse([
            'featured_count' => $featuredCount,
            'max_featured' => 6
        ], 'Featured reviews count fetched successfully');
    }
}