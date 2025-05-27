<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrivacyPolicyController extends Controller
{
    /**
     * Display a listing of privacy policies.
     */
    public function index()
    {
        $policies = PrivacyPolicy::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'privacy_policies' => $policies
        ]);
    }

    /**
     * Store a newly created privacy policy.
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

        $policy = PrivacyPolicy::create([
            'title' => $request->title,
            'content' => $request->content,
            'version' => $request->version,
            'effective_date' => $request->effective_date,
            'is_active' => $request->is_active ?? false,
        ]);

        // If this policy is set as active, deactivate others
        if ($policy->is_active) {
            $policy->setAsActive();
        }

        return response()->json([
            'message' => 'Polityka prywatności została utworzona',
            'privacy_policy' => $policy
        ], 201);
    }

    /**
     * Display the specified privacy policy.
     */
    public function show(PrivacyPolicy $privacyPolicy)
    {
        return response()->json([
            'privacy_policy' => $privacyPolicy
        ]);
    }

    /**
     * Update the specified privacy policy.
     */
    public function update(Request $request, PrivacyPolicy $privacyPolicy)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'version' => 'required|string|max:20',
            'effective_date' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $privacyPolicy->update([
            'title' => $request->title,
            'content' => $request->content,
            'version' => $request->version,
            'effective_date' => $request->effective_date,
            'is_active' => $request->is_active ?? false,
        ]);

        // If this policy is set as active, deactivate others
        if ($privacyPolicy->is_active) {
            $privacyPolicy->setAsActive();
        }

        return response()->json([
            'message' => 'Polityka prywatności została zaktualizowana',
            'privacy_policy' => $privacyPolicy
        ]);
    }

    /**
     * Remove the specified privacy policy.
     */
    public function destroy(PrivacyPolicy $privacyPolicy)
    {
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
    }

    /**
     * Set the specified privacy policy as active.
     */
    public function setActive(PrivacyPolicy $privacyPolicy)
    {
        $privacyPolicy->setAsActive();

        return response()->json([
            'message' => 'Polityka prywatności została ustawiona jako aktywna',
            'privacy_policy' => $privacyPolicy->fresh()
        ]);
    }

    /**
     * Get users who haven't accepted privacy policy.
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
