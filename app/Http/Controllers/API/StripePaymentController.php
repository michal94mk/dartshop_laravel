<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Payment\PaymentService;
use App\Services\Payment\CardValidationService;
use App\Services\OrderService;
use App\Http\Requests\Payment\PaymentIntentRequest;
use App\Http\Requests\Payment\ConfirmPaymentRequest;
use App\Http\Requests\Payment\CardValidationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StripePaymentController extends BaseApiController
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
     * Create payment intent for authenticated user.
     *
     * @return JsonResponse
     */
    public function createIntent(): JsonResponse
    {
        $this->logApiRequest(request(), 'Create Stripe payment intent (user)');
        $result = $this->paymentService->createPaymentIntent();
        return $this->successResponse($result, 'Stripe payment intent created successfully');
    }

    /**
     * Create payment intent for guest user.
     *
     * @param PaymentIntentRequest $request
     * @return JsonResponse
     */
    public function createGuestIntent(PaymentIntentRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Create Stripe payment intent (guest)');
        $result = $this->paymentService->createGuestPaymentIntent(
            $request->getCartItems()
        );
        return $this->successResponse($result, 'Stripe guest payment intent created successfully');
    }

    /**
     * Confirm payment and create order for authenticated or guest user.
     *
     * @param ConfirmPaymentRequest $request
     * @return JsonResponse
     */
    public function confirmPayment(ConfirmPaymentRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Confirm Stripe payment');
        $paymentIntentId = $request->getPaymentIntentId();
        $existingOrder = $this->orderService->orderExistsByPaymentIntent($paymentIntentId);
        if ($existingOrder) {
            return $this->successResponse([
                'message' => 'Zamówienie już istnieje',
                'order' => $existingOrder->load('items')
            ], 'Order already exists');
        }
        $paymentIntent = $this->paymentService->getPaymentIntent($paymentIntentId);
        if ($paymentIntent->status !== 'succeeded') {
            return $this->errorResponse('Płatność nie została potwierdzona', 400);
        }
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
        return $this->successResponse([
            'message' => 'Zamówienie zostało utworzone pomyślnie',
            'order' => $order->load('items')
        ], 'Order created successfully');
    }

    /**
     * Check payment status.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Check Stripe payment status');
        $validated = $this->validateRequest($request, [
            'payment_intent_id' => 'required|string',
        ]);
        $result = $this->paymentService->checkPaymentStatus(
            $validated['payment_intent_id']
        );
        return $this->successResponse($result, 'Payment status checked successfully');
    }

    /**
     * Test card number validation.
     *
     * @param CardValidationRequest $request
     * @return JsonResponse
     */
    public function testCardValidation(CardValidationRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Test card validation');
        $cardNumber = $request->getCardNumber();
        $isValid = $this->cardValidationService->validateCardNumber($cardNumber);
        $cardBrand = $this->cardValidationService->detectCardBrand($cardNumber);
        return $this->successResponse([
            'card_number' => $this->cardValidationService->maskCardNumber($cardNumber),
            'is_valid' => $isValid,
            'card_brand' => $cardBrand,
            'message' => $isValid ? 'Numer karty jest prawidłowy' : 'Numer karty jest nieprawidłowy',
            'test_cards' => $this->cardValidationService->getTestCards()
        ], 'Card validation tested successfully');
    }
} 