<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Services\UserOrderService;

class UserOrderController extends BaseApiController
{
    protected $userOrderService;

    public function __construct(UserOrderService $userOrderService)
    {
        $this->userOrderService = $userOrderService;
    }
    /**
     * Get all orders for the authenticated user
     */
    public function myOrders(Request $request): JsonResponse
    {
        $this->logApiRequest($request, 'Fetch user orders');
        $user = Auth::user();
        $orders = $this->userOrderService->getUserOrders($user);
        return $this->successResponse($orders, 'User orders fetched successfully');
    }
    /**
     * Get a specific order for the authenticated user
     */
    public function show(int $id): JsonResponse
    {
        $this->logApiRequest(request(), "Fetch user order for ID: {$id}");
        $user = Auth::user();
        $order = $this->userOrderService->getUserOrder($user, $id);
        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }
        return $this->successResponse($order, 'User order fetched successfully');
    }
} 