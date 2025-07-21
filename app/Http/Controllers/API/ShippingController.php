<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Shipping",
 *     description="API Endpoints for shipping methods"
 * )
 */

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Get shipping methods for public access
     *
     * @OA\Get(
     *     path="/api/shipping-methods",
     *     summary="Get shipping methods",
     *     description="Get available shipping methods with costs",
     *     tags={"Shipping"},
     *     @OA\Parameter(
     *         name="cart_total",
     *         in="query",
     *         description="Cart total amount",
     *         required=true,
     *         @OA\Schema(type="number", format="float", example=299.99)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="methods", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", example="standard"),
     *                 @OA\Property(property="name", type="string", example="Standard Delivery"),
     *                 @OA\Property(property="cost", type="number", format="float", example=15.00),
     *                 @OA\Property(property="estimated_days", type="integer", example=3)
     *             )),
     *             @OA\Property(property="free_shipping_threshold", type="number", format="float", example=500.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
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
     *
     * @OA\Get(
     *     path="/api/user/shipping-methods",
     *     summary="Get user shipping methods",
     *     description="Get shipping methods for authenticated users with additional benefits",
     *     tags={"Shipping"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="cart_total",
     *         in="query",
     *         description="Cart total amount",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=299.99)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="methods", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", example="standard"),
     *                 @OA\Property(property="name", type="string", example="Standard Delivery"),
     *                 @OA\Property(property="cost", type="number", format="float", example=15.00),
     *                 @OA\Property(property="estimated_days", type="integer", example=3)
     *             )),
     *             @OA\Property(property="free_shipping_threshold", type="number", format="float", example=500.00),
     *             @OA\Property(property="cart_qualifies_for_free_shipping", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
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