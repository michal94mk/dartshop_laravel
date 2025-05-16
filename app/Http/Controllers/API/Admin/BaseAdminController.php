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
        $this->middleware(['auth:sanctum', 'role:admin']);
    }
    
    /**
     * Get the default pagination size
     * 
     * @param Request $request
     * @return int
     */
    protected function getPerPage(Request $request): int
    {
        return $request->per_page ?? 15;
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
     * Log and return an error response
     * 
     * @param string $message
     * @param int $code
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code = 400, $data = [])
    {
        Log::error('Admin API Error: ' . $message, $data);
        
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $data,
        ], $code);
    }
} 