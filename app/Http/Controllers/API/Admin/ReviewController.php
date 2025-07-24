<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\ReviewAdminService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ReviewRequest;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

/**
 * API controller for admin review management.
 * Handles requests for listing, creating, updating, deleting, approving, rejecting, and featuring reviews.
 */
class ReviewController extends BaseApiController
{
    /**
     * @var ReviewAdminService
     */
    protected $reviewAdminService;

    /**
     * Inject the review admin service.
     *
     * @param ReviewAdminService $reviewAdminService
     */
    public function __construct(ReviewAdminService $reviewAdminService)
    {
        $this->reviewAdminService = $reviewAdminService;
    }

    /**
     * Get a paginated list of reviews with filters and sorting.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $reviews = $this->reviewAdminService->getReviewsWithFilters($request);
        return $this->paginatedResponse($reviews);
    }

    /**
     * Store a newly created review in storage.
     *
     * @param  \App\Http\Requests\Admin\ReviewRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReviewRequest $request)
    {
        $validated = $request->validated();

        try {
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
            
            $review = Review::create($validated);
            return $this->successResponse('Recenzja została utworzona', $review, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate key error
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'reviews_user_id_product_id_unique') !== false) {
                return $this->errorResponse('Ten użytkownik już dodał recenzję dla tego produktu. Każdy użytkownik może dodać tylko jedną recenzję per produkt.', 422);
            }
            return $this->errorResponse('Błąd podczas tworzenia recenzji: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas tworzenia recenzji: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $review = Review::with(['product', 'user'])->findOrFail($id);
            return $this->successResponse('Recenzja pobrana', $review);
        } catch (\Exception $e) {
            return $this->errorResponse('Recenzja nie została znaleziona', 404);
        }
    }

    /**
     * Update the specified review in storage.
     *
     * @param  \App\Http\Requests\Admin\ReviewRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReviewRequest $request, $id)
    {
        $validated = $request->validated();

        try {
            $review = Review::findOrFail($id);
            
            // Check featured limit if trying to make a review featured
            if (isset($validated['is_featured']) && $validated['is_featured'] && !$review->is_featured) {
                // Check if review is approved
                if (!($validated['is_approved'] ?? $review->is_approved)) {
                    return $this->errorResponse('Recenzja musi być zatwierdzona, aby ją wyróżnić.', 422);
                }
                
                $featuredCount = Review::approvedAndFeatured()->count();
                if ($featuredCount >= 6) {
                    return $this->errorResponse('Możesz wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji, aby dodać nową.', 422);
                }
            }
            
            $review->update($validated);
            return $this->successResponse('Recenzja została zaktualizowana', $review);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate key error
            if ($e->getCode() == 23000 && strpos($e->getMessage(), 'reviews_user_id_product_id_unique') !== false) {
                return $this->errorResponse('Ten użytkownik już dodał recenzję dla tego produktu. Każdy użytkownik może dodać tylko jedną recenzję per produkt.', 422);
            }
            return $this->errorResponse('Błąd podczas aktualizacji recenzji: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas aktualizacji recenzji: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified review from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();
            return $this->successResponse('Recenzja została usunięta');
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas usuwania recenzji: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Approve a review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->is_approved = true;
            $review->save();
            return $this->successResponse('Recenzja została zatwierdzona', $review);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas zatwierdzania recenzji: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Reject a review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->is_approved = false;
            $review->save();
            return $this->successResponse('Recenzja została odrzucona', $review);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas odrzucania recenzji: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Toggle featured status of a review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleFeatured($id)
    {
        try {
            $review = Review::findOrFail($id);
            
            // If trying to feature a review, check if we already have 6 featured reviews
            if (!$review->is_featured) {
                // Check if review is approved
                if (!$review->is_approved) {
                    return $this->errorResponse('Recenzja musi być zatwierdzona, aby ją wyróżnić.', 422);
                }
                
                $featuredCount = Review::approvedAndFeatured()->count();
                if ($featuredCount >= 6) {
                    return $this->errorResponse('Możesz wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji, aby dodać nową.', 422);
                }
            }
            
            $review->is_featured = !$review->is_featured;
            $review->save();
            
            $message = $review->is_featured 
                ? 'Recenzja została wyróżniona.' 
                : 'Recenzja została usunięta z wyróżnienia.';
                
            return response()->json([
                'review' => $review,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas przełączania statusu wyróżnienia: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get form data for creating/editing reviews.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFormData()
    {
        try {
            $users = User::select('id', 'name', 'email')->get();
            $products = Product::select('id', 'name')->get();
            
            return response()->json([
                'users' => $users,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas pobierania danych formularza: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get featured reviews count.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFeaturedCount()
    {
        try {
            $featuredCount = Review::approvedAndFeatured()->count();
            
            return response()->json([
                'featured_count' => $featuredCount,
                'max_featured' => 6
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Błąd podczas pobierania liczby wyróżnionych: ' . $e->getMessage(), 500);
        }
    }
}