<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends BaseApiController
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'cart_total' => 'required|numeric|min:0'
        ]);
        $cartTotal = (float) $request->query('cart_total', 0);
        return $this->successResponse([
            'methods' => $this->shippingService->getShippingMethodsWithCosts($cartTotal),
            'free_shipping_threshold' => $this->shippingService->getFreeShippingThreshold()
        ]);
    }

    public function userShippingMethods(Request $request)
    {
        $user = Auth::user();
        $cartTotal = (float) $request->query('cart_total', 0);

        $methods = $this->shippingService->getShippingMethodsWithCosts($cartTotal);
        $freeShippingThreshold = $this->shippingService->getFreeShippingThreshold();

        return $this->successResponse([
            'methods' => $methods,
            'free_shipping_threshold' => $freeShippingThreshold,
            'cart_qualifies_for_free_shipping' => $cartTotal >= $freeShippingThreshold
        ]);
    }
} 