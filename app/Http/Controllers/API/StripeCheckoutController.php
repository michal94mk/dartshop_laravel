<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Payment\PaymentService;
use App\Services\OrderService;
use App\Http\Requests\Payment\CheckoutSessionRequest;
use App\Http\Requests\Payment\GuestCheckoutSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

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
     * Create checkout session for authenticated user.
     *
     * @param CheckoutSessionRequest $request
     * @return JsonResponse
     */
    public function createSession(CheckoutSessionRequest $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Create Stripe checkout session (user)');
            $result = $this->paymentService->createCheckoutSession(
                $request->getShippingData(),
                $request->getShippingMethod()
            );
            return $this->successResponse($result, 'Stripe checkout session created successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Creating Stripe checkout session (user)');
        }
    }

    /**
     * Create checkout session for guest user.
     *
     * @param GuestCheckoutSessionRequest $request
     * @return JsonResponse
     */
    public function createGuestSession(GuestCheckoutSessionRequest $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Create Stripe checkout session (guest)');
            $result = $this->paymentService->createGuestCheckoutSession(
                $request->getCartItems(),
                $request->getShippingData(),
                $request->getShippingMethod()
            );
            return $this->successResponse($result, 'Stripe guest checkout session created successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Creating Stripe checkout session (guest)');
        }
    }

    /**
     * Handle successful checkout session completion.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleSuccess(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Handle Stripe checkout success');
            $validated = $this->validateRequest($request, [
                'session_id' => 'required|string',
            ]);
            $sessionId = $validated['session_id'];
            $existingOrder = $this->orderService->orderExistsByStripeSession($sessionId);
            if ($existingOrder) {
                return $this->successResponse([
                    'message' => 'Zamówienie już istnieje',
                    'order' => $existingOrder->load('items')
                ], 'Order already exists');
            }
            $session = $this->paymentService->getCheckoutSession($sessionId);
            if ($session->payment_status !== 'paid') {
                return $this->errorResponse('Płatność nie została potwierdzona', 400);
            }
            $order = $this->createOrderFromSession($session);
            return $this->successResponse([
                'message' => 'Zamówienie zostało utworzone pomyślnie',
                'order' => $order->load('items')
            ], 'Order created successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Handling Stripe checkout success');
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
            $user = \App\Models\User::findOrFail($metadata['user_id']);
            return $this->orderService->createOrderFromCart(
                $user,
                $shippingData,
                $shippingMethod,
                $session->payment_intent,
                $session->id
            );
        } else {
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