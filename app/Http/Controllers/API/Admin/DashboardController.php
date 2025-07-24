<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends BaseApiController
{
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
            
            return $this->successResponse('Dane do panelu administracyjnego pobrane pomyślnie', [
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
            Log::error('Dashboard data fetch failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'admin_id' => auth()->id()
            ]);
            
            return $this->errorResponse('Błąd podczas pobierania danych do panelu administracyjnego: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Get sales data for chart
     *
     * @return array
     */
    private function getSalesData()
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Sales data fetch failed', [
                'error' => $e->getMessage(),
                'method' => __METHOD__
            ]);
            
            return [];
        }
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
            // Log the error for debugging purposes
            Log::error('Top products fetch failed', [
                'error' => $e->getMessage(),
                'method' => __METHOD__,
                'admin_id' => auth()->id()
            ]);
            
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
            // Log the error for debugging purposes
            Log::error('Categories data fetch failed', [
                'error' => $e->getMessage(),
                'method' => __METHOD__,
                'admin_id' => auth()->id()
            ]);
            
            return [];
        }
    }
} 