<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends BaseApiController
{
    /**
     * Get all orders for the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myOrders(Request $request)
    {
        $user = Auth::user();
        $orders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return $this->successResponse($orders);
    }
    
    /**
     * Get a specific order for the authenticated user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $order = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }
        return $this->successResponse($order);
    }
} 