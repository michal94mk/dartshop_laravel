<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Stripe Webhook",
 *     description="API Endpoints for Stripe webhook handling"
 * )
 */

class StripeWebhookController extends Controller
{
    /**
     * Obsługa webhooków Stripe
     *
     * @OA\Post(
     *     path="/api/stripe/webhook",
     *     summary="Handle Stripe webhook",
     *     description="Handle Stripe webhook events (payment_intent.succeeded, payment_intent.payment_failed, checkout.session.completed)",
     *     tags={"Stripe Webhook"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string", example="payment_intent.succeeded"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Webhook processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid signature or webhook error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid signature")
     *         )
     *     )
     * )
     */
    public function handleWebhook(Request $request)
    {
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
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id
        ]);

        // Obsługa różnych typów zdarzeń
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
                return response()->json(['status' => 'received']);
        }
    }

    /**
     * Obsługa udanej płatności
     */
    private function handlePaymentIntentSucceeded($event)
    {
        $paymentIntent = $event->data->object;
        
        Log::info('Payment succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown'
        ]);

        // Znajdź zamówienie po payment_intent_id
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

        return response()->json(['status' => 'success']);
    }

    /**
     * Obsługa nieudanej płatności
     */
    private function handlePaymentIntentFailed($event)
    {
        $paymentIntent = $event->data->object;
        
        Log::error('Payment failed', [
            'payment_intent_id' => $paymentIntent->id,
            'last_payment_error' => $paymentIntent->last_payment_error ?? null
        ]);

        // Znajdź zamówienie po payment_intent_id
        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
        
        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled'
            ]);
        }

        return response()->json(['status' => 'failed']);
    }

    /**
     * Obsługa przetwarzania płatności (dla Przelewy24)
     */
    private function handlePaymentIntentProcessing($event)
    {
        $paymentIntent = $event->data->object;
        
        Log::info('Payment processing (Przelewy24)', [
            'payment_intent_id' => $paymentIntent->id,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown'
        ]);

        // Znajdź zamówienie po payment_intent_id
        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
        
        if ($order) {
            $order->update([
                'payment_status' => 'processing'
            ]);
        }

        return response()->json(['status' => 'processing']);
    }

    /**
     * Obsługa ukończonej sesji checkout
     */
    private function handleCheckoutSessionCompleted($event)
    {
        $session = $event->data->object;
        
        Log::info('Checkout session completed', [
            'session_id' => $session->id,
            'payment_intent_id' => $session->payment_intent,
            'payment_status' => $session->payment_status
        ]);

        return response()->json(['status' => 'session_completed']);
    }
} 