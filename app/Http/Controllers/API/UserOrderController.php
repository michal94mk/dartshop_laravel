<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
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
            
        return response()->json([
            'data' => $orders
        ]);
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
            ->firstOrFail();
            
        return response()->json($order);
    }
} 