<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Wyświetlanie formularza dodawania recenzji dla produktu
     */
    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Sprawdzamy, czy użytkownik już dodał recenzję dla tego produktu
        $existingReview = null;
        
        if (Auth::check()) {
            $existingReview = Review::where('user_id', Auth::id())
                                   ->where('product_id', $productId)
                                   ->first();
        }
        
        return view('frontend.reviews.create', compact('product', 'existingReview'));
    }
    
    /**
     * Zapisywanie nowej recenzji
     */
    public function store(Request $request, $productId)
    {
        // Walidacja danych
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|max:255',
            'content' => 'required|min:10',
        ]);
        
        // Sprawdzamy, czy produkt istnieje
        $product = Product::findOrFail($productId);
        
        // Sprawdzamy, czy użytkownik już dodał recenzję dla tego produktu
        $existingReview = Review::where('user_id', Auth::id())
                               ->where('product_id', $productId)
                               ->first();
        
        if ($existingReview) {
            return back()->with('error', 'Już dodałeś recenzję dla tego produktu.');
        }
        
        // Tworzymy nową recenzję
        $review = new Review([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_approved' => false,
            'is_featured' => false
        ]);
        
        $review->save();
        
        return redirect()->route('frontend.products.show', $productId)
                        ->with('success', 'Dziękujemy za dodanie recenzji! Zostanie ona opublikowana po zatwierdzeniu przez administratora.');
    }
}
