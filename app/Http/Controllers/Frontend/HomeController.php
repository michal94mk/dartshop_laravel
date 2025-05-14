<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\Promotion;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Display the homepage with newest products
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $newestProducts = Product::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('frontend.home', ['newestProducts' => $newestProducts]);
    }

    /**
     * Get newest products for homepage display
     * 
     * @return \Illuminate\View\View
     */
    public function indexNewestProductsForHomepage()
    {
        $newestProducts = Product::with(['category', 'brand'])
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
        
        $activePromotions = Promotion::where('is_active', true)
            ->where('starts_at', '<=', Carbon::now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', Carbon::now());
            })
            ->take(4)
            ->get();
            
        return view('frontend.home', [
            'newestProducts' => $newestProducts,
            'activePromotions' => $activePromotions
        ]);
    }

    /**
     * Display filtered list of products for regular users
     * 
     * This method handles product filtering, sorting and pagination.
     * It can return either HTML view or JSON response depending on the request.
     *
     * @param Request $request Query parameters for filtering and sorting:
     *                         - filter[categories]: array of category IDs
     *                         - filter[price_min]: minimum price
     *                         - filter[price_max]: maximum price
     *                         - filter[brands]: array of brand IDs
     *                         - sort: price-asc, price-desc, name-asc, name-desc
     *                         - paginate: number of items per page
     * @return View|JsonResponse
     */
    public function indexForRegularUsers(Request $request): View|JsonResponse
    {
        $filters = $request->query('filter');
        $paginate = $request->input('paginate', 9);
        $sort = $request->input('sort');
        $query = Product::query();

        // Apply sorting based on request parameters
        if (!is_null($sort)) {
            if ($sort === 'price-asc') {
                $query = $query->orderBy('price', 'ASC');
            } elseif ($sort === 'price-desc') {
                $query = $query->orderBy('price', 'DESC');
            } elseif ($sort === 'name-asc') {
                $query = $query->orderBy('name', 'ASC');
            } elseif ($sort === 'name-desc') {
                $query = $query->orderBy('name', 'DESC');
            }
        }

        // Apply filters if provided
        if (!is_null($filters)) {
            // Filter by categories
            if (array_key_exists('categories', $filters)) {
                $query = $query->whereIn('category_id', $filters['categories']);
            }
            
            // Apply price range filters
            if (!is_null($filters['price_min'])) {
                $query = $query->where('price', '>=', $filters['price_min']);
            }
            if (!is_null($filters['price_max'])) {
                $query = $query->where('price', '<=', $filters['price_max']);
            }
            
            // Filter by brands
            if (array_key_exists('brands', $filters)) {
                $query = $query->whereIn('brand_id', $filters['brands']);
            }

            // Return JSON response when filters are applied (for AJAX requests)
            $products = $query->with('category', 'brand')->paginate($paginate);

            return response()->json([
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]);
        }

        // Default view with all products and filter options
        $products = Product::with('category', 'brand')->paginate($paginate);
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('frontend.categories.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Display the about page
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('home.about');
    }

    /**
     * Display the contact page
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('home.contact');
    }

    /**
     * Display detailed information for a specific product
     *
     * @param int $id Product ID
     * @return \Illuminate\View\View
     */
    public function showProduct($id)
    {
        $product = Product::with(['category', 'brand'])->find($id);
        return view('frontend.product', compact('product'));
    }
} 