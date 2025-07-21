<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\PrivacyPolicy;
use App\Http\Requests\Admin\PrivacyPolicyRequest;
use Illuminate\Support\Facades\Log;

class PrivacyPolicyController extends BaseAdminController
{
    /**
     * Get a list of privacy policies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $policies = PrivacyPolicy::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'privacy_policies' => $policies
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get privacy policies', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error fetching privacy policies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created privacy policy.
     *
     * @param PrivacyPolicyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PrivacyPolicyRequest $request)
    {
        try {
            $policy = PrivacyPolicy::create($request->validated());

            // If this policy is set as active, deactivate others
            if ($policy->is_active) {
                $policy->setAsActive();
            }

            return response()->json([
                'message' => 'Polityka prywatności została utworzona',
                'privacy_policy' => $policy
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create privacy policy', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error creating privacy policy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified privacy policy.
     *
     * @param PrivacyPolicy $privacyPolicy
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(PrivacyPolicy $privacyPolicy)
    {
        try {
            return response()->json([
                'privacy_policy' => $privacyPolicy
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get privacy policy', [
                'error' => $e->getMessage(),
                'policy_id' => $privacyPolicy->id ?? null,
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error fetching privacy policy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified privacy policy.
     *
     * @param PrivacyPolicyRequest $request
     * @param PrivacyPolicy $privacyPolicy
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PrivacyPolicyRequest $request, PrivacyPolicy $privacyPolicy)
    {
        try {
            $privacyPolicy->update($request->validated());

            // If this policy is set as active, deactivate others
            if ($privacyPolicy->is_active) {
                $privacyPolicy->setAsActive();
            }

            return response()->json([
                'message' => 'Polityka prywatności została zaktualizowana',
                'privacy_policy' => $privacyPolicy
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update privacy policy', [
                'error' => $e->getMessage(),
                'policy_id' => $privacyPolicy->id,
                'request_data' => $request->validated(),
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error updating privacy policy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified privacy policy.
     *
     * @param PrivacyPolicy $privacyPolicy
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PrivacyPolicy $privacyPolicy)
    {
        try {
            // Don't allow deletion of active policy
            if ($privacyPolicy->is_active) {
                return response()->json([
                    'message' => 'Nie można usunąć aktywnej polityki prywatności'
                ], 422);
            }

            $privacyPolicy->delete();

            return response()->json([
                'message' => 'Polityka prywatności została usunięta'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete privacy policy', [
                'error' => $e->getMessage(),
                'policy_id' => $privacyPolicy->id,
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error deleting privacy policy: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set the specified privacy policy as active.
     *
     * @param PrivacyPolicy $privacyPolicy
     * @return \Illuminate\Http\JsonResponse
     */
    public function setActive(PrivacyPolicy $privacyPolicy)
    {
        try {
            $privacyPolicy->setAsActive();

            return response()->json([
                'message' => 'Polityka prywatności została ustawiona jako aktywna',
                'privacy_policy' => $privacyPolicy->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set privacy policy as active', [
                'error' => $e->getMessage(),
                'policy_id' => $privacyPolicy->id,
                'method' => __METHOD__
            ]);
            
            return response()->json([
                'error' => 'Error setting privacy policy as active: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users who have not accepted the privacy policy.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersWithoutAcceptance()
    {
        $users = \App\Models\User::where('privacy_policy_accepted', false)
            ->orWhereNull('privacy_policy_accepted')
            ->select('id', 'name', 'email', 'created_at', 'privacy_policy_accepted_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($users);
    }

    /**
     * Get privacy policy acceptance statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAcceptanceStats()
    {
        $totalUsers = \App\Models\User::count();
        $acceptedUsers = \App\Models\User::where('privacy_policy_accepted', true)->count();
        $notAcceptedUsers = $totalUsers - $acceptedUsers;
        
        $acceptanceRate = $totalUsers > 0 ? round(($acceptedUsers / $totalUsers) * 100, 2) : 0;

        $recentAcceptances = \App\Models\User::where('privacy_policy_accepted', true)
            ->whereNotNull('privacy_policy_accepted_at')
            ->where('privacy_policy_accepted_at', '>=', now()->subDays(30))
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
