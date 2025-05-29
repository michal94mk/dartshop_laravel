<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsOfService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TermsOfServiceController extends Controller
{
    /**
     * Display a listing of terms of service.
     */
    public function index()
    {
        $terms = TermsOfService::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'terms_of_service' => $terms
        ]);
    }

    /**
     * Store a newly created terms of service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $terms = TermsOfService::create([
            'title' => $request->title,
            'content' => $request->content,
            'version' => $request->version,
            'effective_date' => $request->effective_date,
            'is_active' => $request->is_active ?? false,
        ]);

        // If this terms is set as active, deactivate others
        if ($terms->is_active) {
            $terms->setAsActive();
        }

        return response()->json([
            'message' => 'Regulamin został utworzony',
            'terms_of_service' => $terms
        ], 201);
    }

    /**
     * Display the specified terms of service.
     */
    public function show(TermsOfService $termsOfService)
    {
        return response()->json([
            'terms_of_service' => $termsOfService
        ]);
    }

    /**
     * Update the specified terms of service.
     */
    public function update(Request $request, TermsOfService $termsOfService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $termsOfService->update([
            'title' => $request->title,
            'content' => $request->content,
            'version' => $request->version,
            'effective_date' => $request->effective_date,
            'is_active' => $request->is_active ?? false,
        ]);

        // If this terms is set as active, deactivate others
        if ($termsOfService->is_active) {
            $termsOfService->setAsActive();
        }

        return response()->json([
            'message' => 'Regulamin został zaktualizowany',
            'terms_of_service' => $termsOfService
        ]);
    }

    /**
     * Remove the specified terms of service.
     */
    public function destroy(TermsOfService $termsOfService)
    {
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
    }

    /**
     * Set the specified terms of service as active.
     */
    public function setActive(TermsOfService $termsOfService)
    {
        $termsOfService->setAsActive();

        return response()->json([
            'message' => 'Regulamin został ustawiony jako aktywny',
            'terms_of_service' => $termsOfService->fresh()
        ]);
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