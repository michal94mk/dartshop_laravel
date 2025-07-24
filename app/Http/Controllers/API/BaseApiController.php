<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseApiController extends Controller
{
    /**
     * Send a success response
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse(mixed $data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Send an error response
     *
     * @param string $message
     * @param int $code
     * @param array|null $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Send a not found response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Send a validation error response
     *
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Send an unauthorized response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Send a forbidden response
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Send a conflict response (409)
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function conflictResponse(string $message = 'Conflict'): JsonResponse
    {
        return $this->errorResponse($message, 409);
    }

    /**
     * Send a too many requests response (429)
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function tooManyRequestsResponse(string $message = 'Too many requests'): JsonResponse
    {
        return $this->errorResponse($message, 429);
    }

    /**
     * Send a server error response
     *
     * @param string $message
     * @param Exception|null $exception
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Internal server error', ?Exception $exception = null): JsonResponse
    {
        if ($exception) {
            Log::error('API Error', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }

        return $this->errorResponse($message, 500);
    }

    /**
     * Handle exceptions in a standardized way
     *
     * @param Exception $exception
     * @param string $context
     * @return JsonResponse
     */
    protected function handleException(Exception $exception, string $context = 'API operation'): JsonResponse
    {
        Log::error("Error in {$context}", [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Handle specific exceptions
        if ($exception instanceof ValidationException) {
            return $this->validationErrorResponse($exception->errors(), 'Validation failed');
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->notFoundResponse('Resource not found');
        }

        if ($exception instanceof AuthorizationException) {
            return $this->forbiddenResponse($exception->getMessage());
        }

        // In production, don't expose internal errors
        if (config('app.debug')) {
            return $this->errorResponse($exception->getMessage(), 500);
        }

        return $this->serverErrorResponse();
    }

    /**
     * Send a paginated response
     *
     * @param LengthAwarePaginator $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function paginatedResponse(LengthAwarePaginator $data, ?string $message = null): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages(),
            ]
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    /**
     * Send a created response (201)
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function createdResponse(mixed $data = null, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Send an accepted response (202)
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function acceptedResponse(mixed $data = null, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message, 202);
    }

    /**
     * Send a no content response (204)
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Send a response with custom status code
     *
     * @param mixed $data
     * @param int $code
     * @param string|null $message
     * @return JsonResponse
     */
    protected function customResponse(mixed $data, int $code, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message, $code);
    }

    /**
     * Send a response with metadata
     *
     * @param mixed $data
     * @param array $meta
     * @param string|null $message
     * @return JsonResponse
     */
    protected function responseWithMeta(mixed $data, array $meta, ?string $message = null): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    /**
     * Send a response with cache information
     *
     * @param mixed $data
     * @param bool $fromCache
     * @param string|null $message
     * @return JsonResponse
     */
    protected function responseWithCache(mixed $data, bool $fromCache, ?string $message = null): JsonResponse
    {
        return $this->responseWithMeta($data, [
            'cached' => $fromCache,
            'timestamp' => now()->toISOString(),
        ], $message);
    }

    /**
     * Validate request and return validated data or throw exception
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $attributes
     * @return array
     * @throws ValidationException
     */
    protected function validateRequest(Request $request, array $rules, array $messages = [], array $attributes = []): array
    {
        return $request->validate($rules, $messages, $attributes);
    }

    /**
     * Check if user has permission and throw exception if not
     *
     * @param string $ability
     * @param mixed $arguments
     * @return void
     * @throws AuthorizationException
     */
    protected function authorizeAction(string $ability, ...$arguments): void
    {
        $this->authorize($ability, $arguments);
    }

    /**
     * Log API request for debugging
     *
     * @param Request $request
     * @param string $context
     * @return void
     */
    protected function logApiRequest(Request $request, string $context = 'API Request'): void
    {
        if (config('app.debug')) {
            Log::info($context, [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'params' => $request->all(),
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
            ]);
        }
    }
} 