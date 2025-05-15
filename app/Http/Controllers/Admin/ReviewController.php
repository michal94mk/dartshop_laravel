<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Maksymalna liczba wyróżnionych recenzji
    const MAX_FEATURED_REVIEWS = 5;

    /**
     * Wyświetla listę recenzji
     */
    public function index()
    {
        $pendingReviews = Review::with(['user', 'product'])
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $approvedReviews = Review::with(['user', 'product'])
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Liczba wyróżnionych recenzji
        $featuredCount = Review::where('is_featured', true)->count();
            
        return view('admin.reviews.index', compact('pendingReviews', 'approvedReviews', 'featuredCount'));
    }
    
    /**
     * Wyświetla szczegóły recenzji
     */
    public function show(Review $review)
    {
        $review->load(['user', 'product']);
        $featuredCount = Review::where('is_featured', true)->count();
        return view('admin.reviews.show', compact('review', 'featuredCount'));
    }
    
    /**
     * Wyświetla formularz edycji recenzji
     */
    public function edit(Review $review)
    {
        $review->load(['user', 'product']);
        $featuredCount = Review::where('is_featured', true)->count();
        return view('admin.reviews.edit', compact('review', 'featuredCount'));
    }
    
    /**
     * Aktualizuje recenzję
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'is_approved' => 'boolean',
            'is_featured' => 'boolean'
        ]);
        
        // Jeśli próbujemy wyróżnić recenzję, sprawdzamy limit
        if (isset($validated['is_featured']) && $validated['is_featured'] && !$review->is_featured) {
            $featuredCount = Review::where('is_featured', true)->count();
            
            if ($featuredCount >= self::MAX_FEATURED_REVIEWS) {
                return redirect()->route('admin.reviews.edit', $review)
                    ->with('error', 'Osiągnięto limit wyróżnionych recenzji (' . self::MAX_FEATURED_REVIEWS . '). Usuń wyróżnienie z innej recenzji, aby wyróżnić tę.');
            }
        }
        
        $review->update($validated);
        
        return redirect()->route('admin.reviews.show', $review)
            ->with('success', 'Recenzja została zaktualizowana.');
    }
    
    /**
     * Zatwierdza recenzję
     */
    public function approve(Review $review)
    {
        $review->update([
            'is_approved' => true
        ]);
        
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Recenzja została zatwierdzona.');
    }
    
    /**
     * Odrzuca recenzję
     */
    public function reject(Review $review)
    {
        $review->update([
            'is_approved' => false
        ]);
        
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Recenzja została odrzucona.');
    }
    
    /**
     * Włącza/wyłącza wyróżnienie recenzji
     */
    public function toggleFeatured(Review $review)
    {
        // Jeśli próbujemy wyróżnić recenzję
        if (!$review->is_featured) {
            // Sprawdzamy aktualną liczbę wyróżnionych recenzji
            $featuredCount = Review::where('is_featured', true)->count();
            
            // Jeśli osiągnięto limit, wyświetlamy błąd
            if ($featuredCount >= self::MAX_FEATURED_REVIEWS) {
                return redirect()->back()
                    ->with('error', 'Osiągnięto limit wyróżnionych recenzji (' . self::MAX_FEATURED_REVIEWS . '). Usuń wyróżnienie z innej recenzji, aby wyróżnić tę.');
            }
            
            $review->update(['is_featured' => true]);
            $status = 'wyróżniona';
        } else {
            // Jeśli zdejmujemy wyróżnienie, po prostu to robimy
            $review->update(['is_featured' => false]);
            $status = 'usunięta z wyróżnionych';
        }
        
        return redirect()->back()
            ->with('success', "Recenzja została $status.");
    }
    
    /**
     * Usuwa recenzję
     */
    public function destroy(Review $review)
    {
        $review->delete();
        
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Recenzja została usunięta.');
    }
}
