<?php

namespace App\Services\Admin;

use App\Models\NewsletterSubscription;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service for handling admin newsletter business logic (listing, filtering, exporting, deleting, stats)
 */
class NewsletterAdminService
{
    /**
     * Get paginated, filtered, and sorted newsletter subscriptions with stats.
     *
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getSubscriptionsWithFilters(array $filters, int $perPage = 15): array
    {
        $query = NewsletterSubscription::query();

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('email', 'like', "%{$search}%");
        }

        // Status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $allowedSortFields = ['created_at', 'email', 'status', 'verified_at', 'unsubscribed_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        $query->orderBy($sortField, $sortDirection);

        $subscriptions = $query->paginate($perPage);

        $stats = [
            'active' => NewsletterSubscription::where('status', 'active')->count(),
            'pending' => NewsletterSubscription::where('status', 'pending')->count(),
            'unsubscribed' => NewsletterSubscription::where('status', 'unsubscribed')->count(),
            'total' => NewsletterSubscription::count(),
        ];

        return [
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
        ];
    }

    /**
     * Delete a newsletter subscription by ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteById(int $id): void
    {
        $subscription = NewsletterSubscription::findOrFail($id);
        $subscription->delete();
    }

    /**
     * Export newsletter subscriptions to CSV (filtered).
     *
     * @param array $filters
     * @return string CSV data
     */
    public function exportToCsv(array $filters): string
    {
        $query = NewsletterSubscription::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where('email', 'like', "%{$filters['search']}%");
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

        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF"); // BOM for UTF-8
        foreach ($csvData as $row) {
            fputcsv($handle, $row, ';');
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        return $csv;
    }

    /**
     * Get newsletter statistics for dashboard.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total' => NewsletterSubscription::count(),
            'active' => NewsletterSubscription::where('status', 'active')->count(),
            'pending' => NewsletterSubscription::where('status', 'pending')->count(),
            'unsubscribed' => NewsletterSubscription::where('status', 'unsubscribed')->count(),
            'recent' => NewsletterSubscription::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }
} 