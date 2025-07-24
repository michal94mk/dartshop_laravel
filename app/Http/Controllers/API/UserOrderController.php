<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Exception;

class UserOrderController extends BaseApiController
{
    /**
     * Get all orders for the authenticated user
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function myOrders(Request $request): JsonResponse
    {
        try {
            $this->logApiRequest($request, 'Fetch user orders');
            $user = Auth::user();
            $orders = Order::with(['items.product'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->successResponse($orders, 'User orders fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, 'Fetching user orders');
        }
    }
    
    /**
     * Get a specific order for the authenticated user
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $this->logApiRequest(request(), "Fetch user order for ID: {$id}");
            $user = Auth::user();
            $order = Order::with(['items.product'])
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->first();
            if (!$order) {
                return $this->notFoundResponse('Order not found');
            }
            return $this->successResponse($order, 'User order fetched successfully');
        } catch (Exception $e) {
            return $this->handleException($e, "Fetching user order for ID: {$id}");
        }
    }
} 