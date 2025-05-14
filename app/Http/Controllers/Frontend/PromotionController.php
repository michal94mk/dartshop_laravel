<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class PromotionController extends Controller
{
    /**
     * Display promotions page for frontend users.
     */
    public function showPromotions()
    {
        // Frontend view for customers
        return view('frontend.promotions');
    }
} 