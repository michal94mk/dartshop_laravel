<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionController extends Controller
{
    /**
     * Display promotions page for frontend users.
     */
    public function showPromotions()
    {
        // Get active promotions
        $activePromotions = Promotion::where('is_active', true)
            ->where('starts_at', '<=', Carbon::now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', Carbon::now());
            })
            ->get();

        return view('frontend.promotions', [
            'activePromotions' => $activePromotions
        ]);
    }
} 