<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Services\Payment\CardValidationService;
use App\Services\OrderService;
use App\Http\Requests\Payment\PaymentIntentRequest;
use App\Http\Requests\Payment\ConfirmPaymentRequest;
use App\Http\Requests\Payment\CardValidationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripePaymentController extends Controller
{
    protected $paymentService;
    protected $cardValidationService;
    protected $orderService;

    public function __construct(
        PaymentService $paymentService,
        CardValidationService $cardValidationService,
        OrderService $orderService
    ) {
        $this->paymentService = $paymentService;
        $this->cardValidationService = $cardValidationService;
        $this->orderService = $orderService;
    }

    /**
     * Create payment intent for authenticated user
     */
    public function createIntent(): JsonResponse
    {
        try {
            $result = $this->paymentService->createPaymentIntent();
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error creating payment intent', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Błąd podczas tworzenia płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create payment intent for guest user
     */
    public function createGuestIntent(PaymentIntentRequest $request): JsonResponse
    {
        try {
            $result = $this->paymentService->createGuestPaymentIntent(
                $request->getCartItems()
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error creating guest payment intent', [
                'error' => $e->getMessage(),
                'cart_items' => $request->getCartItems()
            ]);

            return response()->json([
                'message' => 'Błąd podczas tworzenia płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm payment and create order for authenticated user
     */
    public function confirmPayment(ConfirmPaymentRequest $request): JsonResponse
    {
        try {
            $paymentIntentId = $request->getPaymentIntentId();

            // Check if order already exists
            $existingOrder = $this->orderService->orderExistsByPaymentIntent($paymentIntentId);
            if ($existingOrder) {
                return response()->json([
                    'message' => 'Zamówienie już istnieje',
                    'order' => $existingOrder->load('items')
                ]);
            }

            // Check payment status in Stripe
            $paymentIntent = $this->paymentService->getPaymentIntent($paymentIntentId);
            
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'message' => 'Płatność nie została potwierdzona'
                ], 400);
            }

            // Create order
            if ($request->isGuestPayment()) {
                $order = $this->orderService->createOrderFromGuestCart(
                    $request->getCartItems(),
                    $request->getShippingData(),
                    $request->getShippingMethod(),
                    $paymentIntentId
                );
            } else {
                $order = $this->orderService->createOrderFromCart(
                    Auth::user(),
                    $request->getShippingData(),
                    $request->getShippingMethod(),
                    $paymentIntentId
                );
            }

            return response()->json([
                'message' => 'Zamówienie zostało utworzone pomyślnie',
                'order' => $order->load('items')
            ]);

        } catch (\Exception $e) {
            Log::error('Error confirming payment', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $request->getPaymentIntentId(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Błąd podczas przetwarzania zamówienia: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        try {
            $result = $this->paymentService->checkPaymentStatus(
                $request->payment_intent_id
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $request->payment_intent_id
            ]);

            return response()->json([
                'message' => 'Błąd podczas sprawdzania statusu płatności: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test card number validation
     */
    public function testCardValidation(CardValidationRequest $request): JsonResponse
    {
        try {
            $cardNumber = $request->getCardNumber();
            $isValid = $this->cardValidationService->validateCardNumber($cardNumber);
            $cardBrand = $this->cardValidationService->detectCardBrand($cardNumber);

            return response()->json([
                'card_number' => $this->cardValidationService->maskCardNumber($cardNumber),
                'is_valid' => $isValid,
                'card_brand' => $cardBrand,
                'message' => $isValid ? 'Numer karty jest prawidłowy' : 'Numer karty jest nieprawidłowy',
                'test_cards' => $this->cardValidationService->getTestCards()
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating card', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Błąd podczas walidacji karty: ' . $e->getMessage()
            ], 500);
        }
    }
} 