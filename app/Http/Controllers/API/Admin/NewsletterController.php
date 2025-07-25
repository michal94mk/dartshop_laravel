<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\NewsletterAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NewsletterController extends BaseApiController
{
    private NewsletterAdminService $newsletterAdminService;

    public function __construct(NewsletterAdminService $newsletterAdminService)
    {
        $this->newsletterAdminService = $newsletterAdminService;
    }

    /**
     * Display a listing of newsletter subscriptions
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        $perPage = $request->get('per_page', 15);
        $result = $this->newsletterAdminService->getSubscriptionsWithFilters($filters, $perPage);
        return $this->successResponse($result, 'Subskrybenci newslettera pobrani pomyślnie');
    }

    /**
     * Remove the specified newsletter subscription
     */
    public function destroy($id): JsonResponse
    {
        $this->newsletterAdminService->deleteById($id);
        return $this->successResponse(null, 'Subskrypcja została usunięta');
    }

    /**
     * Export newsletter subscriptions to CSV
     */
    public function export(Request $request)
    {
        $filters = $request->all();
        $csv = $this->newsletterAdminService->exportToCsv($filters);
        $filename = 'newsletter-subscriptions-' . date('Y-m-d') . '.csv';
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get newsletter statistics for dashboard
     */
    public function stats(): JsonResponse
    {
        $stats = $this->newsletterAdminService->getStats();
        return response()->json($stats);
    }
}
