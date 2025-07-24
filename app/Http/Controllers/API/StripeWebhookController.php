<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Exception;

class StripeWebhookController extends BaseApiController
{
    /**
     * Obsługa webhooków Stripe
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Stripe webhook received');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Invalid signature', 400);
        } catch (Exception $e) {
            return $this->handleException($e, 'Stripe webhook signature verification');
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id
        ]);

        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event);
                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event);
                case 'checkout.session.completed':
                    return $this->handleCheckoutSessionCompleted($event);
                case 'payment_intent.processing':
                    return $this->handlePaymentIntentProcessing($event);
                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'type' => $event->type
                    ]);
                    return $this->noContentResponse();
            }
        } catch (Exception $e) {
            return $this->handleException($e, 'Stripe webhook event handling');
        }
    }

    /**
     * Obsługa udanej płatności
     */
    private function handlePaymentIntentSucceeded($event): JsonResponse
    {
        $paymentIntent = $event->data->object;
        Log::info('Payment succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown'
        ]);
        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing'
            ]);
            Log::info('Order updated after successful payment', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id
            ]);
        }
        return $this->successResponse(['status' => 'success']);
    }

    /**
     * Obsługa nieudanej płatności
     */
    private function handlePaymentIntentFailed($event): JsonResponse
    {
        $paymentIntent = $event->data->object;
        Log::error('Payment failed', [
            'payment_intent_id' => $paymentIntent->id,
            'last_payment_error' => $paymentIntent->last_payment_error ?? null
        ]);
        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled'
            ]);
        }
        return $this->errorResponse('Payment failed', 400);
    }

    /**
     * Obsługa przetwarzania płatności (dla Przelewy24)
     */
    private function handlePaymentIntentProcessing($event): JsonResponse
    {
        $paymentIntent = $event->data->object;
        Log::info('Payment processing (Przelewy24)', [
            'payment_intent_id' => $paymentIntent->id,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown'
        ]);
        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
        if ($order) {
            $order->update([
                'payment_status' => 'processing'
            ]);
        }
        return $this->successResponse(['status' => 'processing']);
    }

    /**
     * Obsługa ukończonej sesji checkout
     */
    private function handleCheckoutSessionCompleted($event): JsonResponse
    {
        $session = $event->data->object;
        Log::info('Checkout session completed', [
            'session_id' => $session->id,
            'payment_intent_id' => $session->payment_intent,
            'payment_status' => $session->payment_status
        ]);
        return $this->successResponse(['status' => 'session_completed']);
    }
} 