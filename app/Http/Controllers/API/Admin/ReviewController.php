<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\ReviewRequest;

class ReviewController extends BaseAdminController
{
    /**
     * Display a listing of the reviews.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Review::with(['product', 'user']);
            
            // Apply filters
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
            
            if ($request->has('approved') && $request->approved !== null) {
                $query->where('is_approved', $request->approved == 'true');
            }
            
            if ($request->has('featured') && $request->featured !== null) {
                $query->where('is_featured', $request->featured == 'true');
            }
            
            // Apply sorting
            $sortField = $request->sort_field ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            
            if ($sortField === 'product') {
                $query->join('products', 'reviews.product_id', '=', 'products.id')
                      ->orderBy('products.name', $sortDirection)
                      ->select('reviews.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
            
            // Paginate results
            $perPage = $this->getPerPage($request);
            $reviews = $query->paginate($perPage);
            
            return response()->json($reviews);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching reviews: ' . $e->getMessage(), 500);
        }
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
                    return $this->errorResponse('Recenzja musi być zatwierdzona, aby mogła być wyróżniona.', 422);
                }
                
                $featuredCount = Review::approvedAndFeatured()->count();
                if ($featuredCount >= 6) {
                    return $this->errorResponse('Można wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji przed dodaniem nowego.', 422);
                }
            }
            
            $review = Review::create($validated);
            return response()->json($review, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating review: ' . $e->getMessage(), 500);
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
            return response()->json($review);
        } catch (\Exception $e) {
            return $this->errorResponse('Review not found', 404);
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
                    return $this->errorResponse('Recenzja musi być zatwierdzona, aby mogła być wyróżniona.', 422);
                }
                
                $featuredCount = Review::approvedAndFeatured()->count();
                if ($featuredCount >= 6) {
                    return $this->errorResponse('Można wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji przed dodaniem nowego.', 422);
                }
            }
            
            $review->update($validated);
            return response()->json($review);
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating review: ' . $e->getMessage(), 500);
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
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting review: ' . $e->getMessage(), 500);
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
            return response()->json($review);
        } catch (\Exception $e) {
            return $this->errorResponse('Error approving review: ' . $e->getMessage(), 500);
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
            return response()->json($review);
        } catch (\Exception $e) {
            return $this->errorResponse('Error rejecting review: ' . $e->getMessage(), 500);
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
                    return $this->errorResponse('Recenzja musi być zatwierdzona, aby mogła być wyróżniona.', 422);
                }
                
                $featuredCount = Review::approvedAndFeatured()->count();
                if ($featuredCount >= 6) {
                    return $this->errorResponse('Można wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji przed dodaniem nowego.', 422);
                }
            }
            
            $review->is_featured = !$review->is_featured;
            $review->save();
            
            $message = $review->is_featured 
                ? 'Recenzja została wyróżniona.' 
                : 'Recenzja została usunięta z wyróżnionych.';
                
            return response()->json([
                'review' => $review,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Error toggling featured status: ' . $e->getMessage(), 500);
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
            return $this->errorResponse('Error fetching form data: ' . $e->getMessage(), 500);
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
            return $this->errorResponse('Error fetching featured count: ' . $e->getMessage(), 500);
        }
    }
} 