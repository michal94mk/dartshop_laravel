<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Admin/Dashboard",
 *     description="API Endpoints for admin dashboard"
 * )
 */

class DashboardController extends BaseAdminController
{
    /**
     * Get admin dashboard statistics
     *
     * @OA\Get(
     *     path="/api/admin/dashboard",
     *     summary="Get dashboard statistics",
     *     description="Retrieve admin dashboard statistics and data",
     *     tags={"Admin/Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="counts", type="object",
     *                 @OA\Property(property="products", type="integer", example=150),
     *                 @OA\Property(property="users", type="integer", example=500),
     *                 @OA\Property(property="orders", type="integer", example=75),
     *                 @OA\Property(property="reviews", type="integer", example=200)
     *             ),
     *             @OA\Property(property="recent_orders", type="array", @OA\Items(ref="#/components/schemas/RecentOrder")),
     *             @OA\Property(property="sales_data", type="array", @OA\Items(ref="#/components/schemas/SalesData")),
     *             @OA\Property(property="top_products", type="array", @OA\Items(ref="#/components/schemas/TopProduct")),
     *             @OA\Property(property="categories_data", type="array", @OA\Items(ref="#/components/schemas/CategoryData"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Get counts
            $productCount = Product::count();
            $userCount = User::count();
            $orderCount = Order::count();
            $reviewCount = Review::count();
            
            // Get recent orders
            $recentOrders = Order::with(['user'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'total' => $order->total,
                        'status' => $order->status,
                        'user' => $order->user ? [
                            'id' => $order->user->id,
                            'name' => $order->user->name,
                            'email' => $order->user->email,
                        ] : null,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    ];
                });
            
            // Get sales data for the last 7 days
            $salesData = $this->getSalesData();
            
            // Get top selling products
            $topProducts = $this->getTopProducts();
            
            // Get categories data
            $categoriesData = $this->getCategoriesData();
            
            return response()->json([
                'counts' => [
                    'products' => $productCount,
                    'users' => $userCount,
                    'orders' => $orderCount,
                    'reviews' => $reviewCount,
                ],
                'recent_orders' => $recentOrders,
                'sales_data' => $salesData,
                'top_products' => $topProducts,
                'categories_data' => $categoriesData,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching dashboard data: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get sales data for chart
     *
     * @return array
     */
    private function getSalesData()
    {
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        $salesData = Order::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Fill in missing dates with zero values
        $dateRange = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $dateRange[$formattedDate] = [
                'date' => $formattedDate,
                'total_sales' => 0,
                'order_count' => 0
            ];
        }
        
        foreach ($salesData as $record) {
            $dateRange[$record->date] = [
                'date' => $record->date,
                'total_sales' => (float)$record->total_sales,
                'order_count' => (int)$record->order_count
            ];
        }
        
        return array_values($dateRange);
    }
    
    /**
     * Get top selling products
     *
     * @return array
     */
    private function getTopProducts()
    {
        // This assumes you have order_items table with product_id and quantity
        // Adjust according to your actual database schema
        try {
            $topProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.price * order_items.quantity) as total_sales')
                )
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_sales', 'desc')
                ->limit(5)
                ->get();
            
            return $topProducts;
        } catch (\Exception $e) {
            // If there's an error (like missing tables), return empty array
            return [];
        }
    }
    
    /**
     * Get categories data for pie chart
     *
     * @return array
     */
    private function getCategoriesData()
    {
        try {
            $categoriesData = DB::table('categories')
                ->leftJoin('products', 'categories.id', '=', 'products.category_id')
                ->select(
                    'categories.id',
                    'categories.name',
                    DB::raw('COUNT(products.id) as products_count')
                )
                ->groupBy('categories.id', 'categories.name')
                ->having('products_count', '>', 0)
                ->orderBy('products_count', 'desc')
                ->get();
            
            return $categoriesData;
        } catch (\Exception $e) {
            // If there's an error, return empty array
            return [];
        }
    }
} 