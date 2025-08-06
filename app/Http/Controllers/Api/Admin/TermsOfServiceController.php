<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Admin\TermsOfServiceRequest;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\Admin\TermsOfServiceAdminService;

class TermsOfServiceController extends BaseApiController
{
    protected $termsService;

    public function __construct(TermsOfServiceAdminService $termsService)
    {
        $this->termsService = $termsService;
    }

    /**
     * Display a listing of terms of service.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $terms = $this->termsService->getAll();
            return $this->successResponse($terms, 'Regulamin pobrany');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Fetching terms of service');
        }
    }

    /**
     * Store a newly created terms of service.
     *
     * @param  \App\Http\Requests\Admin\TermsOfServiceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TermsOfServiceRequest $request)
    {
        try {
            $terms = $this->termsService->create($request->validated());
            return $this->createdResponse($terms, 'Regulamin został utworzony');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Creating terms of service');
        }
    }

    /**
     * Display the specified terms of service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $terms = $this->termsService->getById($id);
            if (!$terms) {
                return $this->notFoundResponse('Terms of service not found');
            }
            return $this->successResponse($terms, 'Regulamin pobrany');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Fetching terms of service by ID');
        }
    }

    /**
     * Update the specified terms of service.
     *
     * @param  \App\Http\Requests\Admin\TermsOfServiceRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TermsOfServiceRequest $request, $id)
    {
        try {
            $terms = $this->termsService->getById($id);
            if (!$terms) {
                return $this->notFoundResponse('Terms of service not found');
            }
            $updated = $this->termsService->update($terms, $request->validated());
            return $this->successResponse($updated, 'Regulamin został zaktualizowany');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Updating terms of service');
        }
    }

    /**
     * Remove the specified terms of service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $terms = $this->termsService->getById($id);
            if (!$terms) {
                return $this->notFoundResponse('Terms of service not found');
            }
            $this->termsService->delete($terms);
            return $this->successResponse(null, 'Regulamin został usunięty');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Deleting terms of service');
        }
    }

    /**
     * Set the specified terms of service as active.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setActive($id)
    {
        try {
            $terms = $this->termsService->getById($id);
            if (!$terms) {
                return $this->notFoundResponse('Terms of service not found');
            }
            $active = $this->termsService->setActive($terms);
            return $this->successResponse($active, 'Regulamin został ustawiony jako aktywny');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Setting terms of service as active');
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

        return $this->paginatedResponse($users);
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

        return $this->successResponse([
            'total_users' => $totalUsers,
            'accepted_users' => $acceptedUsers,
            'not_accepted_users' => $notAcceptedUsers,
            'acceptance_rate' => $acceptanceRate,
            'recent_acceptances' => $recentAcceptances
        ], 'Terms of service acceptance stats fetched successfully');
    }
} 