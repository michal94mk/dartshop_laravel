<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\NewsletterSubscription;
use App\Services\NewsletterService;
use App\Http\Requests\Frontend\NewsletterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NewsletterController extends BaseApiController
{
    protected $newsletterService;

    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }
    /**
     * Subscribe to the newsletter
     *
     * @param NewsletterRequest $request
     * @return JsonResponse
     */
    public function subscribe(NewsletterRequest $request): JsonResponse
    {
        Log::info('Newsletter subscribe endpoint hit', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);
        
        try {
            $email = $request->validated()['email'];
            $result = $this->newsletterService->subscribe($email);
            
            Log::info('Newsletter subscribe response', [
                'response' => $result,
                'subscription_id' => $result['subscription']->id,
                'email' => $result['subscription']->email
            ]);
            
            return $this->successResponse($result);
        } catch (\Exception $e) {
            Log::info('Newsletter subscribe error response', [
                'error_message' => $e->getMessage(),
                'email' => $request->validated()['email'] ?? 'unknown'
            ]);
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Verify email subscription (for direct browser links).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string'
        ]);
        
        // Verification logic is handled in the service
        $success = $this->newsletterService->verifySubscription($validated['token']);
        
        if (!$success) {
            return redirect('/newsletter/verify?status=error&message=' . urlencode('Token nieprawidłowy lub wygasły'));
        }
        return redirect('/newsletter/verified?message=' . urlencode('Email został pomyślnie zweryfikowany!'));
    }

    /**
     * Verify email subscription (API endpoint for SPA).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string'
        ]);
        
        // Verification logic is handled in the service
        $success = $this->newsletterService->verifySubscription($validated['token']);
        
        if (!$success) {
            return $this->errorResponse('Invalid or expired verification token');
        }
        
        return $this->successResponse(null, 'Email został pomyślnie zweryfikowany!');
    }

    /**
     * Unsubscribe from the newsletter.
     *
     * @param NewsletterRequest $request
     * @return JsonResponse
     */
    public function unsubscribe(NewsletterRequest $request): JsonResponse
    {
        // Unsubscribe logic is handled in the service
        $success = $this->newsletterService->unsubscribe($request->validated()['email']);
        if (!$success) {
            return $this->notFoundResponse('No active subscription found for this email address');
        }
        return $this->successResponse(null, 'You have been unsubscribed from the newsletter');
    }

    /**
     * Check newsletter subscription status.
     *
     * @param NewsletterRequest $request
     * @return JsonResponse
     */
    public function status(NewsletterRequest $request): JsonResponse
    {
        $subscription = NewsletterSubscription::where('email', $request->validated()['email'])->first();

        if (!$subscription) {
            return $this->successResponse([
                'subscribed' => false,
                'status' => null
            ]);
        }

        return $this->successResponse([
            'subscribed' => $subscription->isActive(),
            'status' => $subscription->status,
            'verified_at' => $subscription->verified_at?->format('Y-m-d H:i:s')
        ]);
    }
}
