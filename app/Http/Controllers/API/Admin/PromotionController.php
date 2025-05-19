<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PromotionController extends BaseAdminController
{
    /**
     * Display a listing of the promotions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promotions = Promotion::latest()->get();
        return response()->json($promotions);
    }

    /**
     * Store a newly created promotion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:promotions',
            'description' => 'nullable|string',
            'type' => 'required|string|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Validate percentage value cannot be greater than 100
        if ($request->type === 'percentage' && $request->value > 100) {
            return $this->validationError(['value' => ['Wartość procentowa nie może przekraczać 100%.']]);
        }

        $promotion = Promotion::create($request->all());
        return response()->json($promotion, 201);
    }

    /**
     * Display the specified promotion.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);
        return response()->json($promotion);
    }

    /**
     * Update the specified promotion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:promotions,code,' . $promotion->id,
            'description' => 'nullable|string',
            'type' => 'sometimes|string|in:percentage,fixed',
            'value' => 'sometimes|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Validate percentage value cannot be greater than 100
        if ($request->type === 'percentage' && $request->value > 100) {
            return $this->validationError(['value' => ['Wartość procentowa nie może przekraczać 100%.']]);
        }

        $promotion->update($request->all());
        return response()->json($promotion);
    }

    /**
     * Remove the specified promotion from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return response()->json(null, 204);
    }

    /**
     * Generate a unique promotion code.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateCode()
    {
        $code = strtoupper(Str::random(8));
        
        // Check if code already exists and regenerate if needed
        while (Promotion::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }
        
        return response()->json(['code' => $code]);
    }
} 