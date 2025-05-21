<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaseAdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware już zdefiniowane w routach
    }
    
    /**
     * Get the per page count from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function getPerPage(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        
        // Limit per page to between 5 and 100
        if ($perPage < 5) {
            $perPage = 5;
        } elseif ($perPage > 100) {
            $perPage = 100;
        }
        
        return $perPage;
    }
    
    /**
     * Format validation errors for API response
     * 
     * @param \Illuminate\Validation\Validator $validator
     * @return array
     */
    protected function formatValidationErrors($validator)
    {
        return [
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ];
    }
    
    /**
     * Return a validation error response
     * 
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationError($errors)
    {
        return $this->errorResponse('Validation error', 422, $errors);
    }
    
    /**
     * Log and return a success response
     * 
     * @param string $message
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($message, $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    
    /**
     * Return error response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  array  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $statusCode = 500, $errors = [])
    {
        // Log error for debugging
        \Illuminate\Support\Facades\Log::error('Admin API Error: ' . $message);
        
        // Create base response array
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        if ($statusCode === 500) {
            // In development, show more details about the error
            if (config('app.debug')) {
                $e = new \Exception($message);
                $response['debug'] = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ];
            }
        }
        
        // Extra debug logging for 422 errors
        if ($statusCode === 422) {
            \Illuminate\Support\Facades\Log::debug('Sending 422 error response', [
                'response' => $response,
                'status_code' => $statusCode
            ]);
        }
        
        return response()->json($response, $statusCode);
    }
} 