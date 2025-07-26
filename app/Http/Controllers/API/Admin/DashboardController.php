<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\DashboardAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $params = $request->all();
        $dashboardData = $this->dashboardAdminService->getDashboardData($params);
        
        return $this->successResponse($dashboardData, 'Dane do panelu administracyjnego pobrane pomyślnie');
    }
} 