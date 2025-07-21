<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\TermsOfService;
use App\Http\Requests\Admin\TermsOfServiceRequest;
use Illuminate\Support\Facades\Log;

class TermsOfServiceController extends BaseAdminController
{
    /**
     * Display a listing of terms of service.
     */
    public function index()
    {
        try {
            $terms = TermsOfService::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'terms_of_service' => $terms
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get terms of service', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error fetching terms of service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created terms of service.
     */
    public function store(TermsOfServiceRequest $request)
    {
        try {
            $terms = TermsOfService::create($request->validated());

            // If this terms is set as active, deactivate others
            if ($terms->is_active) {
                $terms->setAsActive();
            }

            return response()->json([
                'message' => 'Regulamin został utworzony',
                'terms_of_service' => $terms
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create terms of service', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error creating terms of service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified terms of service.
     */
    public function show(TermsOfService $termsOfService)
    {
        try {
            return response()->json([
                'terms_of_service' => $termsOfService
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get terms of service', [
                'error' => $e->getMessage(),
                'terms_id' => $termsOfService->id ?? null,
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error fetching terms of service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified terms of service.
     */
    public function update(TermsOfServiceRequest $request, TermsOfService $termsOfService)
    {
        try {
            $termsOfService->update($request->validated());

            // If this terms is set as active, deactivate others
            if ($termsOfService->is_active) {
                $termsOfService->setAsActive();
            }

            return response()->json([
                'message' => 'Regulamin został zaktualizowany',
                'terms_of_service' => $termsOfService
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update terms of service', [
                'error' => $e->getMessage(),
                'terms_id' => $termsOfService->id,
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error updating terms of service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified terms of service.
     */
    public function destroy(TermsOfService $termsOfService)
    {
        try {
            // Don't allow deletion of active terms
            if ($termsOfService->is_active) {
                return response()->json([
                    'message' => 'Nie można usunąć aktywnego regulaminu'
                ], 422);
            }

            $termsOfService->delete();

            return response()->json([
                'message' => 'Regulamin został usunięty'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete terms of service', [
                'error' => $e->getMessage(),
                'terms_id' => $termsOfService->id,
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error deleting terms of service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set the specified terms of service as active.
     */
    public function setActive(TermsOfService $termsOfService)
    {
        try {
            $termsOfService->setAsActive();

            return response()->json([
                'message' => 'Regulamin został ustawiony jako aktywny',
                'terms_of_service' => $termsOfService->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set terms of service as active', [
                'error' => $e->getMessage(),
                'terms_id' => $termsOfService->id,
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error setting terms of service as active: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users who haven't accepted terms of service.
     */
    public function getUsersWithoutAcceptance()
    {
        $users = \App\Models\User::where('terms_of_service_accepted', false)
            ->orWhereNull('terms_of_service_accepted')
            ->select('id', 'name', 'email', 'created_at', 'terms_of_service_accepted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($users);
    }

    /**
     * Get terms of service acceptance statistics.
     */
    public function getAcceptanceStats()
    {
        $totalUsers = \App\Models\User::count();
        $acceptedUsers = \App\Models\User::where('terms_of_service_accepted', true)->count();
        $notAcceptedUsers = $totalUsers - $acceptedUsers;
        
        $acceptanceRate = $totalUsers > 0 ? round(($acceptedUsers / $totalUsers) * 100, 2) : 0;

        $recentAcceptances = \App\Models\User::where('terms_of_service_accepted', true)
            ->whereNotNull('terms_of_service_accepted_at')
            ->where('terms_of_service_accepted_at', '>=', now()->subDays(30))
            ->count();

        return response()->json([
            'total_users' => $totalUsers,
            'accepted_users' => $acceptedUsers,
            'not_accepted_users' => $notAcceptedUsers,
            'acceptance_rate' => $acceptanceRate,
            'recent_acceptances' => $recentAcceptances
        ]);
    }
} 