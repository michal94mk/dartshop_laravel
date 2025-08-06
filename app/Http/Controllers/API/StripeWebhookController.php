<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Services\Payment\PaymentWebhookService;

class StripeWebhookController extends BaseApiController
{
    protected $webhookService;

    public function __construct(PaymentWebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }
    /**
     * Handle Stripe webhooks (payment and checkout events).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Stripe webhook received');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            return $this->errorResponse('Invalid signature', 400);
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id
        ]);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->webhookService->handlePaymentIntentSucceeded($event->data->object);
                return $this->successResponse(['status' => 'success']);
            case 'payment_intent.payment_failed':
                $this->webhookService->handlePaymentIntentFailed($event->data->object);
                return $this->errorResponse('Payment failed', 400);
            case 'checkout.session.completed':
                $this->webhookService->handleCheckoutSessionCompleted($event->data->object);
                return $this->successResponse(['status' => 'session_completed']);
            case 'payment_intent.processing':
                $this->webhookService->handlePaymentIntentProcessing($event->data->object);
                return $this->successResponse(['status' => 'processing']);
            default:
                Log::info('Unhandled Stripe webhook event', [
                    'type' => $event->type
                ]);
                return $this->noContentResponse();
        }
    }
} 