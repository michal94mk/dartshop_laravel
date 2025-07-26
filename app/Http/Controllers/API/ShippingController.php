<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ShippingController extends BaseApiController
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Get available shipping methods and free shipping threshold.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch shipping methods');
        
        $validated = $this->validateRequest($request, [
            'cart_total' => 'required|numeric|min:0'
        ]);
        $cartTotal = (float) $validated['cart_total'];
        return $this->successResponse([
            'methods' => $this->shippingService->getShippingMethodsWithCosts($cartTotal),
            'free_shipping_threshold' => $this->shippingService->getFreeShippingThreshold()
        ], 'Shipping methods fetched successfully');
    }

    /**
     * Get available shipping methods for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userShippingMethods(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch user shipping methods');
        
        $user = Auth::user();
        $cartTotal = (float) $request->query('cart_total', 0);

        $methods = $this->shippingService->getShippingMethodsWithCosts($cartTotal);
        $freeShippingThreshold = $this->shippingService->getFreeShippingThreshold();

        return $this->successResponse([
            'methods' => $methods,
            'free_shipping_threshold' => $freeShippingThreshold,
            'cart_qualifies_for_free_shipping' => $cartTotal >= $freeShippingThreshold
        ], 'User shipping methods fetched successfully');
    }
} 