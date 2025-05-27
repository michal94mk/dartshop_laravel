<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        // Skip rate limiting for non-API routes
        if (!$request->is('api/*')) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);
        
        // Get current attempts
        $attempts = Cache::get($key, 0);
        
        // Check if limit exceeded
        if ($attempts >= $maxAttempts) {
            Log::warning('API Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->fullUrl(),
                'attempts' => $attempts,
                'limit' => $maxAttempts,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Przekroczono limit żądań. Spróbuj ponownie za chwilę.',
                'retry_after' => $decayMinutes * 60,
            ], 429);
        }

        // Increment attempts
        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        $response = $next($request);

        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $attempts - 1));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->timestamp);

        return $response;
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        // Use user ID if authenticated, otherwise use IP
        if ($request->user()) {
            return 'rate_limit:user:' . $request->user()->id;
        }

        return 'rate_limit:ip:' . $request->ip();
    }

    /**
     * Resolve max attempts based on user type
     */
    protected function resolveMaxAttempts(Request $request, int $default): int
    {
        // Authenticated users get higher limits
        if ($request->user()) {
            return $default * 2; // Double the limit for authenticated users
        }

        // Special endpoints might have different limits
        if ($request->is('api/products*')) {
            return $default; // Standard limit for product endpoints
        }

        if ($request->is('api/auth/*')) {
            return 10; // Lower limit for auth endpoints
        }

        return $default;
    }
}
