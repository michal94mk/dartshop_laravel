<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Services\Admin\UserAdminService;
use App\Http\Controllers\Api\BaseApiController;

class UserController extends BaseApiController
{
    protected $userAdminService;

    public function __construct(UserAdminService $userAdminService)
    {
        $this->userAdminService = $userAdminService;
    }

    /**
     * Display a listing of the users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $users = $this->userAdminService->getUsersWithFilters($request);
        return $this->paginatedResponse($users);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \App\Http\Requests\Admin\UserStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        $user = $this->userAdminService->createUser($request->validated());
        return $this->createdResponse($user);
    }

    /**
     * Display the specified user with orders.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = $this->userAdminService->getUserWithOrders($id);
        if (!$user) {
            return $this->notFoundResponse('User not found');
        }
        return $this->successResponse($user, 'User fetched');
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \App\Http\Requests\Admin\UserUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = $this->userAdminService->findUserOrFail($id);
        $updatedUser = $this->userAdminService->updateUser($user, $request->validated());
        return $this->successResponse($updatedUser);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = $this->userAdminService->findUserOrFail($id);
        $this->userAdminService->deleteUser($user);
        return $this->noContentResponse();
    }

    /**
     * Verify the specified user's email.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify($id)
    {
        $user = $this->userAdminService->findUserOrFail($id);
        $this->userAdminService->verifyUser($user);
        return $this->successResponse($user, 'User verified successfully');
    }

    /**
     * Force delete user with all related data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDestroy($id)
    {
        $user = $this->userAdminService->findUserWithTrashedOrFail($id);
        $this->userAdminService->forceDeleteUser($user);
        return $this->noContentResponse();
    }
} 