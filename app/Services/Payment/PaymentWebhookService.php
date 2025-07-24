<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentWebhookService
{
    /**
     * Handle successful payment intent from Stripe webhook.
     *
     * @param object $paymentIntent Stripe payment intent object
     * @return void
     */
    public function handlePaymentIntentSucceeded($paymentIntent): void
    {
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
    }

    /**
     * Handle failed payment intent from Stripe webhook.
     *
     * @param object $paymentIntent Stripe payment intent object
     * @return void
     */
    public function handlePaymentIntentFailed($paymentIntent): void
    {
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
    }

    /**
     * Handle processing payment intent (e.g. Przelewy24) from Stripe webhook.
     *
     * @param object $paymentIntent Stripe payment intent object
     * @return void
     */
    public function handlePaymentIntentProcessing($paymentIntent): void
    {
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
    }

    /**
     * Handle completed checkout session from Stripe webhook.
     *
     * @param object $session Stripe checkout session object
     * @return void
     */
    public function handleCheckoutSessionCompleted($session): void
    {
        Log::info('Checkout session completed', [
            'session_id' => $session->id,
            'payment_intent_id' => $session->payment_intent,
            'payment_status' => $session->payment_status
        ]);
        // Add any additional logic if needed
    }
} 