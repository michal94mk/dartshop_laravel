<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Payment\PaymentService;
use App\Services\Payment\CardValidationService;
use App\Services\OrderService;
use App\Http\Requests\Payment\PaymentIntentRequest;
use App\Http\Requests\Payment\ConfirmPaymentRequest;
use App\Http\Requests\Payment\CardValidationRequest;
use App\Exceptions\PaymentAmountMismatchException;
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
     */
    public function createIntent(PaymentIntentRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Create Stripe payment intent (user)');
        $result = $this->paymentService->createPaymentIntent($request->getShippingMethod());

        return $this->successResponse($result, 'Stripe payment intent created successfully');
    }

    /**
     * Create payment intent for guest user.
     */
    public function createGuestIntent(PaymentIntentRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Create Stripe payment intent (guest)');
        $result = $this->paymentService->createGuestPaymentIntent(
            $request->getCartItems(),
            $request->getShippingMethod()
        );

        return $this->successResponse($result, 'Stripe guest payment intent created successfully');
    }

    /**
     * Confirm payment and create order for authenticated or guest user.
     *
     * Order lines and amounts come from the snapshot frozen at PaymentIntent
     * creation — never from a client-supplied cart.
     */
    public function confirmPayment(ConfirmPaymentRequest $request): JsonResponse
    {
        $this->logApiRequest($request, 'Confirm Stripe payment');
        $paymentIntentId = $request->getPaymentIntentId();
        $existingOrder = $this->orderService->orderExistsByPaymentIntent($paymentIntentId);
        if ($existingOrder) {
            return $this->successResponse([
                'message' => 'Zamówienie już istnieje',
                'order' => $existingOrder->load('items'),
            ], 'Order already exists');
        }

        $paymentIntent = $this->paymentService->getPaymentIntent($paymentIntentId);
        if ($paymentIntent->status !== 'succeeded') {
            return $this->errorResponse('Płatność nie została potwierdzona', 400);
        }

        $snapshot = $this->paymentService->pullCartSnapshot($paymentIntentId);
        if ($snapshot === null) {
            return $this->errorResponse(
                'Brak zaufanego podsumowania koszyka dla tej płatności. Utwórz płatność ponownie.',
                422
            );
        }

        if (($snapshot['shipping_method'] ?? null) !== $request->getShippingMethod()) {
            return $this->errorResponse(
                'Metoda wysyłki nie zgadza się z tą, za którą pobrano płatność.',
                422
            );
        }

        $isGuestSnapshot = (bool) ($snapshot['guest'] ?? false);
        if ($request->isGuestPayment() !== $isGuestSnapshot) {
            return $this->errorResponse('Typ płatności nie zgadza się z PaymentIntent.', 422);
        }

        if (!$isGuestSnapshot) {
            $snapshotUserId = isset($snapshot['user_id']) ? (int) $snapshot['user_id'] : null;
            if ($snapshotUserId === null || $snapshotUserId !== Auth::id()) {
                return $this->errorResponse('Ta płatność nie należy do zalogowanego użytkownika.', 403);
            }
        }

        try {
            $quote = $this->orderService->quoteFromSnapshot($snapshot);
            $this->paymentService->assertPaymentIntentMatchesQuote(
                $paymentIntent,
                $quote['total_cents']
            );
        } catch (PaymentAmountMismatchException $e) {
            return $this->errorResponse(
                'Kwota opłacona w Stripe nie zgadza się z wartością zamówienia.',
                422
            );
        }

        $order = $this->orderService->createOrderFromPaymentSnapshot(
            $snapshot,
            $request->getShippingData(),
            $paymentIntentId,
            $isGuestSnapshot ? null : Auth::id()
        );

        $this->paymentService->forgetCartSnapshot($paymentIntentId);

        return $this->successResponse([
            'message' => 'Zamówienie zostało utworzone pomyślnie',
            'order' => $order->load('items'),
        ], 'Order created successfully');
    }

    /**
     * Check payment status.
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
            'test_cards' => $this->cardValidationService->getTestCards(),
        ], 'Card validation tested successfully');
    }
}
