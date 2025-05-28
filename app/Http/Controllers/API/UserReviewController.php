<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class UserReviewController extends Controller
{
    /**
     * Get all reviews for the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myReviews(Request $request)
    {
        $user = Auth::user();
        $reviews = Review::with('product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'data' => $reviews
        ]);
    }
    
    /**
     * Get a specific review for the authenticated user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $review = Review::with('product')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();
            
        return response()->json($review);
    }

    /**
     * Get all reviews for a specific product with statistics
     *
     * @param  int  $productId
     * @return \\Illuminate\\Http\\Response
     */
    public function getProductReviews($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Get approved reviews with user information
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
                        'name' => $review->user->first_name . ' ' . $review->user->last_name
                    ]
                ];
            });

        // Calculate statistics
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;
        
        // Rating distribution
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $reviews->where('rating', $i)->count();
        }

        $statistics = [
            'reviews_count' => $totalReviews,
            'average_rating' => $averageRating,
            'distribution' => $distribution
        ];

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'statistics' => $statistics
        ]);
    }

    /**
     * Store a new review for a product
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $productId): JsonResponse
    {
        try {
            $user = Auth::user();
            $product = Product::findOrFail($productId);

            // Check if user can review this product
            if (!$product->canBeReviewedBy($user->id)) {
                return response()->json([
                    'error' => 'Już dodałeś recenzję dla tego produktu'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:1000'
            ], [
                'rating.required' => 'Ocena jest wymagana',
                'rating.min' => 'Ocena musi być między 1 a 5',
                'rating.max' => 'Ocena musi być między 1 a 5',
                'title.required' => 'Tytuł recenzji jest wymagany',
                'title.max' => 'Tytuł może mieć maksymalnie 255 znaków',
                'content.required' => 'Treść recenzji jest wymagana',
                'content.max' => 'Treść może mieć maksymalnie 1000 znaków'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Błędy walidacji',
                    'errors' => $validator->errors()
                ], 422);
            }

            $review = Review::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'rating' => $request->rating,
                'title' => $request->title,
                'content' => $request->content,
                'is_approved' => false, // Admin approval required
                'is_featured' => false
            ]);

            $review->load('user:id,name');

            return response()->json([
                'message' => 'Recenzja została dodana i oczekuje na moderację',
                'review' => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Nie udało się dodać recenzji'], 500);
        }
    }

    /**
     * Update user's review
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $reviewId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $reviewId): JsonResponse
    {
        try {
            $user = Auth::user();
            $review = Review::where('user_id', $user->id)
                ->where('id', $reviewId)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|integer|min:1|max:5',
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Błędy walidacji',
                    'errors' => $validator->errors()
                ], 422);
            }

            $review->update($request->only(['rating', 'title', 'content']));
            $review->update(['is_approved' => false]); // Reset approval status
            $review->load('user:id,name');

            return response()->json([
                'message' => 'Recenzja została zaktualizowana i oczekuje na moderację',
                'review' => $review
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Nie udało się zaktualizować recenzji'], 500);
        }
    }

    /**
     * Delete user's review
     *
     * @param  int  $reviewId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($reviewId): JsonResponse
    {
        try {
            $user = Auth::user();
            $review = Review::where('user_id', $user->id)
                ->where('id', $reviewId)
                ->firstOrFail();

            $review->delete();

            return response()->json([
                'message' => 'Recenzja została usunięta'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Nie udało się usunąć recenzji'], 500);
        }
    }

    /**
     * Check if authenticated user can review a product
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function canReview($productId): JsonResponse
    {
        try {
            $user = Auth::user();
            $product = Product::findOrFail($productId);
            
            $canReview = $product->canBeReviewedBy($user->id);
            $existingReview = null;
            
            if (!$canReview) {
                $existingReview = Review::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->first();
            }

            return response()->json([
                'can_review' => $canReview,
                'existing_review' => $existingReview
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Nie udało się sprawdzić uprawnień'], 500);
        }
    }
} 