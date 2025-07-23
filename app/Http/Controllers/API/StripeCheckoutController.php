<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Payment\PaymentService;
use App\Services\OrderService;
use App\Http\Requests\Payment\CheckoutSessionRequest;
use App\Http\Requests\Payment\GuestCheckoutSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StripeCheckoutController extends BaseApiController
{
    protected $paymentService;
    protected $orderService;

    public function __construct(PaymentService $paymentService, OrderService $orderService)
    {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }

    /**
     * Create checkout session for authenticated user
     */
    public function createSession(CheckoutSessionRequest $request): JsonResponse
    {
        try {
            $result = $this->paymentService->createCheckoutSession(
                $request->getShippingData(),
                $request->getShippingMethod()
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error creating checkout session', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Błąd podczas tworzenia sesji płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create checkout session for guest user
     */
    public function createGuestSession(GuestCheckoutSessionRequest $request): JsonResponse
    {
        try {
            $result = $this->paymentService->createGuestCheckoutSession(
                $request->getCartItems(),
                $request->getShippingData(),
                $request->getShippingMethod()
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error creating guest checkout session', [
                'error' => $e->getMessage(),
                'cart_items' => $request->getCartItems()
            ]);

            return response()->json([
                'message' => 'Błąd podczas tworzenia sesji płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle successful checkout session completion
     */
    public function handleSuccess(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);
        try {
            $sessionId = $request->session_id;
            // Check if order already exists
            $existingOrder = $this->orderService->orderExistsByStripeSession($sessionId);
            if ($existingOrder) {
                return $this->successResponse([
                    'message' => 'Zamówienie już istnieje',
                    'order' => $existingOrder->load('items')
                ]);
            }
            // Get session from Stripe
            $session = $this->paymentService->getCheckoutSession($sessionId);
            if ($session->payment_status !== 'paid') {
                return $this->errorResponse('Płatność nie została potwierdzona', 400);
            }
            // Create order based on session metadata
            $order = $this->createOrderFromSession($session);
            return $this->successResponse([
                'message' => 'Zamówienie zostało utworzone pomyślnie',
                'order' => $order->load('items')
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling checkout success', [
                'error' => $e->getMessage(),
                'session_id' => $request->session_id
            ]);
            return $this->serverErrorResponse('Błąd podczas przetwarzania zamówienia: ' . $e->getMessage(), $e);
        }
    }

    /**
     * Create order from Stripe session
     */
    private function createOrderFromSession($session)
    {
        $metadata = $session->metadata;
        $shippingData = json_decode($metadata['shipping_data'], true);
        $shippingMethod = $metadata['shipping_method'] ?? 'courier';

        if (isset($metadata['user_id'])) {
            // Authenticated user order
            $user = \App\Models\User::findOrFail($metadata['user_id']);
            
            return $this->orderService->createOrderFromCart(
                $user,
                $shippingData,
                $shippingMethod,
                $session->payment_intent,
                $session->id
            );
        } else {
            // Guest order
            $cartData = json_decode($metadata['cart_data'], true);
            
            return $this->orderService->createOrderFromGuestCart(
                $cartData,
                $shippingData,
                $shippingMethod,
                $session->payment_intent,
                $session->id
            );
        }
    }
} 