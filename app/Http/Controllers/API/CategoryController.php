<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Return some dummy data for now
        $categories = [
            [
                'id' => 1,
                'name' => 'Lotki',
                'description' => 'Profesjonalne lotki różnych marek',
                'image' => 'https://via.placeholder.com/600x400/indigo/fff?text=Lotki'
            ],
            [
                'id' => 2,
                'name' => 'Tarcze',
                'description' => 'Tarcze elektroniczne i klasyczne',
                'image' => 'https://via.placeholder.com/600x400/indigo/fff?text=Tarcze'
            ],
            [
                'id' => 3,
                'name' => 'Akcesoria',
                'description' => 'Wszelkie akcesoria do gry w dart',
                'image' => 'https://via.placeholder.com/600x400/indigo/fff?text=Akcesoria'
            ]
        ];

        return response()->json($categories);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Return dummy data for a specific category based on ID
        $categories = [
            1 => [
                'id' => 1,
                'name' => 'Lotki',
                'description' => 'Profesjonalne lotki różnych marek',
                'image' => 'https://via.placeholder.com/600x400/indigo/fff?text=Lotki'
            ],
            2 => [
                'id' => 2,
                'name' => 'Tarcze',
                'description' => 'Tarcze elektroniczne i klasyczne',
                'image' => 'https://via.placeholder.com/600x400/indigo/fff?text=Tarcze'
            ],
            3 => [
                'id' => 3,
                'name' => 'Akcesoria',
                'description' => 'Wszelkie akcesoria do gry w dart',
                'image' => 'https://via.placeholder.com/600x400/indigo/fff?text=Akcesoria'
            ]
        ];

        if (!isset($categories[$id])) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($categories[$id]);
    }

    /**
     * Display products for the specified category.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function products($id, Request $request)
    {
        // Dummy implementation - would use real database in production
        $products = [
            1 => [
                [
                    'id' => 1,
                    'name' => 'Lotki Target Agora A30',
                    'description' => 'Profesjonalne lotki ze stali wolframowej 90%',
                    'price' => 149.99,
                    'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Target'
                ],
                [
                    'id' => 4,
                    'name' => 'Lotki Red Dragon Razor Edge',
                    'description' => 'Lotki z wysokiej jakości stali wolframowej',
                    'price' => 129.99,
                    'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Lotki+Red+Dragon'
                ]
            ],
            2 => [
                [
                    'id' => 2,
                    'name' => 'Tarcza elektroniczna Winmau Blade 6',
                    'description' => 'Zaawansowana tarcza dla profesjonalistów',
                    'price' => 299.99,
                    'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Tarcza+Winmau'
                ]
            ],
            3 => [
                [
                    'id' => 3,
                    'name' => 'Zestaw punktowy XQ Max',
                    'description' => 'Zestaw do zapisywania punktów z kredą i ścierką',
                    'price' => 49.99,
                    'image_url' => 'https://via.placeholder.com/300x300/indigo/fff?text=Zestaw+XQ+Max'
                ]
            ]
        ];

        if (!isset($products[$id])) {
            return response()->json(['data' => []], 200);
        }

        // Return with pagination structure for compatibility
        return response()->json([
            'data' => $products[$id],
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => count($products[$id]),
            'total' => count($products[$id])
        ]);
    }
} 