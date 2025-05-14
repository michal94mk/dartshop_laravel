<?php

namespace App\Http\Controllers\Admin;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PromotionController extends BaseAdminController
{
    /**
     * Display a listing of promotions.
     */
    public function index(Request $request)
    {
        $perPage = $this->getPerPage();
        $query = Promotion::query();
        
        // Apply search filter
        $this->applySearch($query, $request, ['name', 'code', 'description']);
        
        $promotions = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        return view('admin.promotions.create');
    }

    /**
     * Store a newly created promotion in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promotions,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
        ]);
        
        // Format dates
        $validated['starts_at'] = Carbon::parse($validated['starts_at']);
        if (!empty($validated['ends_at'])) {
            $validated['ends_at'] = Carbon::parse($validated['ends_at']);
        }
        
        // Set is_active default value if not provided
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Create promotion
        Promotion::create($validated);
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promocja została pomyślnie utworzona.');
    }

    /**
     * Display the specified promotion.
     */
    public function show(Promotion $promotion)
    {
        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified promotion in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promotions,code,' . $promotion->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'usage_limit' => 'nullable|integer|min:1',
        ]);
        
        // Format dates
        $validated['starts_at'] = Carbon::parse($validated['starts_at']);
        if (!empty($validated['ends_at'])) {
            $validated['ends_at'] = Carbon::parse($validated['ends_at']);
        }
        
        // Set is_active value
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Update promotion
        $promotion->update($validated);
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promocja została pomyślnie zaktualizowana.');
    }

    /**
     * Remove the specified promotion from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promocja została pomyślnie usunięta.');
    }
    
    /**
     * Validate promotion code
     */
    public function validateCode(Request $request)
    {
        $code = $request->input('code');
        $orderValue = $request->input('order_value', 0);
        
        $promotion = Promotion::where('code', $code)->first();
        
        if (!$promotion) {
            return response()->json([
                'valid' => false,
                'message' => 'Kod promocyjny jest nieprawidłowy.'
            ]);
        }
        
        if (!$promotion->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Kod promocyjny wygasł lub jest nieaktywny.'
            ]);
        }
        
        if ($promotion->minimum_order_value && $orderValue < $promotion->minimum_order_value) {
            return response()->json([
                'valid' => false,
                'message' => "Minimalna wartość zamówienia to {$promotion->minimum_order_value} zł."
            ]);
        }
        
        $discountAmount = $promotion->calculateDiscountAmount($orderValue);
        
        return response()->json([
            'valid' => true,
            'message' => 'Kod promocyjny został pomyślnie zastosowany.',
            'discount_type' => $promotion->discount_type,
            'discount_value' => $promotion->discount_value,
            'discount_amount' => $discountAmount
        ]);
    }
} 