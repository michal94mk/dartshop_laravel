<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart contents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // For now, return dummy data
        return response()->json([
            'items' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'quantity' => 2,
                    'product' => [
                        'id' => 1,
                        'name' => 'Lotki Target Agora A30',
                        'price' => 149.99,
                        'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Target'
                    ]
                ],
                [
                    'id' => 2,
                    'product_id' => 3,
                    'quantity' => 1,
                    'product' => [
                        'id' => 3,
                        'name' => 'Zestaw punktowy XQ Max',
                        'price' => 49.99,
                        'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Zestaw+XQ+Max'
                    ]
                ]
            ],
            'subtotal' => 349.97,
            'discount' => 0,
            'total' => 349.97
        ]);
    }

    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        // Validation would go here in a real app
        return response()->json([
            'items' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'quantity' => 2,
                    'product' => [
                        'id' => 1,
                        'name' => 'Lotki Target Agora A30',
                        'price' => 149.99,
                        'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Target'
                    ]
                ],
                [
                    'id' => 2,
                    'product_id' => 3,
                    'quantity' => 1,
                    'product' => [
                        'id' => 3,
                        'name' => 'Zestaw punktowy XQ Max',
                        'price' => 49.99,
                        'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Zestaw+XQ+Max'
                    ]
                ],
                [
                    'id' => 3,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'product' => [
                        'id' => $request->product_id,
                        'name' => 'Dodany produkt',
                        'price' => 99.99,
                        'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Nowy+Produkt'
                    ]
                ]
            ],
            'subtotal' => 449.96,
            'discount' => 0,
            'total' => 449.96
        ]);
    }

    /**
     * Remove a product from the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        return response()->json([
            'items' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'quantity' => 2,
                    'product' => [
                        'id' => 1,
                        'name' => 'Lotki Target Agora A30',
                        'price' => 149.99,
                        'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Target'
                    ]
                ]
            ],
            'subtotal' => 299.98,
            'discount' => 0,
            'total' => 299.98
        ]);
    }

    /**
     * Update cart items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        return response()->json([
            'items' => $request->items,
            'subtotal' => 299.98,
            'discount' => 0,
            'total' => 299.98
        ]);
    }

    /**
     * Clear the cart.
     *
     * @return \Illuminate\Http\Response
     */
    public function clear()
    {
        return response()->json([
            'items' => [],
            'subtotal' => 0,
            'discount' => 0,
            'total' => 0
        ]);
    }
} 