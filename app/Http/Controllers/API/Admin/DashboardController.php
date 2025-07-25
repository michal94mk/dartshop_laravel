<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\DashboardAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller for handling dashboard data and statistics
 */
class DashboardController extends BaseApiController
{
    private DashboardAdminService $dashboardAdminService;

    public function __construct(DashboardAdminService $dashboardAdminService)
    {
        $this->dashboardAdminService = $dashboardAdminService;
    }

    /**
     * Get dashboard data including counts, recent orders, sales data, and charts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $params = $request->all();
            $dashboardData = $this->dashboardAdminService->getDashboardData($params);
            
            return $this->successResponse($dashboardData, 'Dane do panelu administracyjnego pobrane pomyślnie');
        } catch (\Exception $e) {
            Log::error('Dashboard data fetch failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'admin_id' => Auth::id() ?? 'unknown'
            ]);
            
            return $this->errorResponse('Błąd podczas pobierania danych do panelu administracyjnego: ' . $e->getMessage(), 500);
        }
    }
} 