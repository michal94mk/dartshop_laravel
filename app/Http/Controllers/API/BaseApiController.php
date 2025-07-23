<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;

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
    protected function successResponse($data = null, ?string $message = null, int $code = 200): JsonResponse
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

        if ($exception instanceof ValidationException) {
            return $this->validationErrorResponse($exception->errors(), 'Validation failed');
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
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function paginatedResponse($data, ?string $message = null): JsonResponse
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
            ]
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response);
    }

    /**
     * Send a created response
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function createdResponse($data = null, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Send a no content response
     *
     * @return JsonResponse
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }
} 