<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

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
} 