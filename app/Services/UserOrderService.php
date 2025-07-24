<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class UserOrderService
{
    /**
     * Get all orders for the given user.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection|Order[]
     */
    public function getUserOrders(User $user)
    {
        return Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get details of a specific order for the given user.
     *
     * @param User $user
     * @param int $orderId
     * @return Order|null
     */
    public function getUserOrder(User $user, int $orderId)
    {
        return Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('id', $orderId)
            ->first();
    }
} 