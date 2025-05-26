<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'is_approved' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $review = Review::create($request->all());
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'sometimes|exists:products,id',
            'user_id' => 'sometimes|exists:users,id',
            'rating' => 'sometimes|integer|min:1|max:5',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|max:1000',
            'is_approved' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $review = Review::findOrFail($id);
            $review->update($request->all());
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
            $review->is_featured = !$review->is_featured;
            $review->save();
            return response()->json($review);
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
} 