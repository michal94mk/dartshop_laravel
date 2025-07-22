<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseAdminController extends Controller
{
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
        // Jeśli komunikat jest po angielsku, zamień na polski (przykładowe tłumaczenia)
        $translations = [
            'created successfully' => 'został(a) utworzony(a) pomyślnie',
            'updated successfully' => 'został(a) zaktualizowany(a) pomyślnie',
            'deleted successfully' => 'został(a) usunięty(a) pomyślnie',
            'fetched successfully' => 'pobrano pomyślnie',
            'order updated successfully' => 'kolejność została zaktualizowana',
            'approved successfully' => 'zatwierdzono pomyślnie',
            'rejected successfully' => 'odrzucono pomyślnie',
            'activated' => 'aktywowany(a)',
            'deactivated' => 'dezaktywowany(a)',
            'featured' => 'wyróżniony(a)',
            'unfeatured' => 'usunięto z wyróżnionych',
        ];
        $msg = $message;
        foreach ($translations as $en => $pl) {
            $msg = preg_replace('/\b' . preg_quote($en, '/') . '\b/i', $pl, $msg);
        }
        return response()->json([
            'success' => true,
            'message' => $msg,
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
        // Przykładowe tłumaczenia błędów
        $translations = [
            'not found' => 'nie znaleziono',
            'error' => 'błąd',
            'validation error' => 'błąd walidacji',
            'forbidden' => 'brak uprawnień',
            'unauthorized' => 'brak autoryzacji',
            'cannot delete' => 'nie można usunąć',
            'already exists' => 'już istnieje',
            'must be approved' => 'musi być zatwierdzony(a)',
            'maximum' => 'maksymalnie',
        ];
        $msg = $message;
        foreach ($translations as $en => $pl) {
            $msg = preg_replace('/\b' . preg_quote($en, '/') . '\b/i', $pl, $msg);
        }
        
        // Create base response array
        $response = [
            'success' => false,
            'message' => $msg,
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