<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * @OA\Tag(
 *     name="Admin/Newsletter",
 *     description="API Endpoints for admin newsletter management"
 * )
 */

class NewsletterController extends BaseAdminController
{
    /**
     * Display a listing of newsletter subscriptions
     *
     * @OA\Get(
     *     path="/api/admin/newsletter",
     *     summary="Get newsletter subscriptions list (admin)",
     *     description="Retrieve all newsletter subscriptions with admin filters and pagination",
     *     tags={"Admin/Newsletter"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status (active, pending, unsubscribed)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "pending", "unsubscribed"})
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter from date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter to date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort_field",
     *         in="query",
     *         description="Sort field (created_at, email, status, verified_at, unsubscribed_at)",
     *         required=false,
     *         @OA\Schema(type="string", default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_direction",
     *         in="query",
     *         description="Sort direction (asc, desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/NewsletterSubscription")),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=75),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=15)
     *             ),
     *             @OA\Property(property="stats", type="object",
     *                 @OA\Property(property="active", type="integer", example=50),
     *                 @OA\Property(property="pending", type="integer", example=10),
     *                 @OA\Property(property="unsubscribed", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=75)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = NewsletterSubscription::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('email', 'like', '%' . $search . '%');
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply sorting
        $sortField = $request->sort_field ?? 'created_at';
        $sortDirection = $request->sort_direction ?? 'desc';
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['created_at', 'email', 'status', 'verified_at', 'unsubscribed_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        $query->orderBy($sortField, $sortDirection);

        // Get stats
        $stats = [
            'active' => NewsletterSubscription::where('status', 'active')->count(),
            'pending' => NewsletterSubscription::where('status', 'pending')->count(),
            'unsubscribed' => NewsletterSubscription::where('status', 'unsubscribed')->count(),
            'total' => NewsletterSubscription::count(),
        ];

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $subscriptions = $query->paginate($perPage);

        return $this->successResponse('Subskrybenci newslettera pobrani pomyślnie', [
            'data' => $subscriptions->items(),
            'pagination' => [
                'current_page' => $subscriptions->currentPage(),
                'last_page' => $subscriptions->lastPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
                'from' => $subscriptions->firstItem(),
                'to' => $subscriptions->lastItem(),
            ],
            'stats' => $stats
        ]);
    }

    /**
     * Remove the specified newsletter subscription
     */
    public function destroy(NewsletterSubscription $newsletter): JsonResponse
    {
        $newsletter->delete();

        return $this->successResponse('Subskrypcja została usunięta');
    }

    /**
     * Export newsletter subscriptions to CSV
     */
    public function export(Request $request)
    {
        $query = NewsletterSubscription::query();

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['Email', 'Status', 'Data subskrypcji', 'Data weryfikacji', 'Data wypisania'];

        foreach ($subscriptions as $subscription) {
            $csvData[] = [
                $subscription->email,
                $subscription->status,
                $subscription->created_at->format('Y-m-d H:i:s'),
                $subscription->verified_at ? $subscription->verified_at->format('Y-m-d H:i:s') : '',
                $subscription->unsubscribed_at ? $subscription->unsubscribed_at->format('Y-m-d H:i:s') : '',
            ];
        }

        $filename = 'newsletter-subscriptions-' . date('Y-m-d') . '.csv';
        
        $handle = fopen('php://temp', 'w+');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fwrite($handle, "\xEF\xBB\xBF");
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row, ';'); // Use semicolon for better Excel compatibility
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

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
        $stats = [
            'total' => NewsletterSubscription::count(),
            'active' => NewsletterSubscription::where('status', 'active')->count(),
            'pending' => NewsletterSubscription::where('status', 'pending')->count(),
            'unsubscribed' => NewsletterSubscription::where('status', 'unsubscribed')->count(),
            'recent' => NewsletterSubscription::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return response()->json($stats);
    }
}
