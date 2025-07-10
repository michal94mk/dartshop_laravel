<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Get shipping methods for public access
     */
    public function index(Request $request)
    {
        $request->validate([
            'cart_total' => 'required|numeric|min:0'
        ]);

        $cartTotal = (float) $request->query('cart_total', 0);

        return response()->json([
            'methods' => $this->shippingService->getShippingMethodsWithCosts($cartTotal),
            'free_shipping_threshold' => $this->shippingService->getFreeShippingThreshold()
        ]);
    }

    /**
     * Get shipping methods for authenticated users
     * This can include user-specific shipping options, discounts, etc.
     */
    public function userShippingMethods(Request $request)
    {
        $user = Auth::user();
        $cartTotal = (float) $request->query('cart_total', 0);

        $methods = $this->shippingService->getShippingMethodsWithCosts($cartTotal);
        $freeShippingThreshold = $this->shippingService->getFreeShippingThreshold();

        return response()->json([
            'methods' => $methods,
            'free_shipping_threshold' => $freeShippingThreshold,
            'cart_qualifies_for_free_shipping' => $cartTotal >= $freeShippingThreshold
        ]);
    }
} 