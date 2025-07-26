<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions with consistent JSON responses
     */
    private function handleApiException(Request $request, Throwable $e)
    {
        $response = [
            'success' => false,
            'message' => 'Internal server error',
        ];

        if ($e instanceof ValidationException) {
            $response['message'] = 'Validation failed';
            $response['errors'] = $e->errors();
            return response()->json($response, 422);
        }

        if ($e instanceof ModelNotFoundException) {
            $response['message'] = 'Resource not found';
            return response()->json($response, 404);
        }

        if ($e instanceof NotFoundHttpException) {
            $response['message'] = 'Endpoint not found';
            return response()->json($response, 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            $response['message'] = 'Method not allowed';
            return response()->json($response, 405);
        }

        if ($e instanceof AuthenticationException) {
            $response['message'] = 'Unauthenticated';
            return response()->json($response, 401);
        }

        if ($e instanceof AuthorizationException) {
            $response['message'] = 'Forbidden';
            return response()->json($response, 403);
        }

        // Log the error for debugging
        Log::error('API Exception: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
        ]);

        // Don't expose internal errors in production
        if (app()->environment('production')) {
            return response()->json($response, 500);
        }

        // In development, show detailed error
        $response['message'] = $e->getMessage();
        $response['debug'] = [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        return response()->json($response, 500);
    }
}
