<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\PrivacyPolicy;
use App\Http\Requests\Admin\PrivacyPolicyRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\BaseApiController;

class PrivacyPolicyController extends BaseApiController
{
    /**
     * Get a list of privacy policies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $policies = PrivacyPolicy::orderBy('created_at', 'desc')->get();
        
        return $this->successResponse($policies, 'Polityki prywatności pobrane pomyślnie');
    }

    /**
     * Store a newly created privacy policy.
     *
     * @param PrivacyPolicyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PrivacyPolicyRequest $request)
    {
        $policy = PrivacyPolicy::create($request->validated());

        // If this policy is set as active, deactivate others
        if ($policy->is_active) {
            $policy->setAsActive();
        }

        return $this->successResponse($policy, 'Polityka prywatności została utworzona');
    }

    /**
     * Display the specified privacy policy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        return $this->successResponse($privacyPolicy, 'Polityka prywatności pobrana pomyślnie');
    }

    /**
     * Update the specified privacy policy.
     *
     * @param  PrivacyPolicyRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PrivacyPolicyRequest $request, $id)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        $privacyPolicy->update($request->validated());

        // If this policy is set as active, deactivate others
        if ($privacyPolicy->is_active) {
            $privacyPolicy->setAsActive();
        }

        return $this->successResponse($privacyPolicy, 'Polityka prywatności została zaktualizowana');
    }

    /**
     * Remove the specified privacy policy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        
        // Don't allow deletion of active policy
        if ($privacyPolicy->is_active) {
            return $this->errorResponse('Nie można usunąć aktywnej polityki prywatności', 422);
        }
        
        $privacyPolicy->delete();
        return $this->successResponse(null, 'Polityka prywatności została usunięta');
    }

    /**
     * Set the specified privacy policy as active.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setActive($id)
    {
        $privacyPolicy = PrivacyPolicy::findOrFail($id);
        $privacyPolicy->setAsActive();

        return $this->successResponse($privacyPolicy->fresh(), 'Polityka prywatności została ustawiona jako aktywna');
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
