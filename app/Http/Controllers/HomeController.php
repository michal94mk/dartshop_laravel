<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    public function index()
{
    $newestProducts = Product::orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    return view('home', ['newestProducts' => $newestProducts]);
}

    public function indexNewestProductsForHomepage()
    {
        $newestProducts = Product::orderBy('created_at', 'desc')->take(8)->get();
        
        if (request()->has('tailwind')) {
            return view('home-tailwind', compact('newestProducts'));
        }
        
        return view('home', compact('newestProducts'));
    }

    public function indexForRegularUsers(Request $request): View|JsonResponse
    {
        $filters = $request->query('filter');
        $paginate = $request->input('paginate', 9);
        $sort = $request->input('sort');
        $query = Product::query();


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

        if (!is_null($filters)) {

            if (array_key_exists('categories', $filters)) {
                $query = $query->whereIn('category_id', $filters['categories']);
            }
            if (!is_null($filters['price_min'])) {
                $query = $query->where('price', '>=', $filters['price_min']);
            }
            if (!is_null($filters['price_max'])) {
                $query = $query->where('price', '<=', $filters['price_max']);
            }
            if (array_key_exists('brands', $filters)) {
                $query = $query->whereIn('brand_id', $filters['brands']);
            }



            $products = $query->with('category', 'brand')->paginate($paginate);

            return response()->json([
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]);
        }

        $products = Product::with('category', 'brand')->paginate($paginate);
        $categories = Category::all();
        $brands = Brand::all();
        
        if (request()->has('tailwind')) {
            return view('frontend.categories.index-tailwind', compact('products', 'categories', 'brands'));
        }
        
        return view('frontend.categories.index', compact('products'));
    }




    public function about()
    {
        return view('home.about');
    }

    public function contact()
    {
        return view('home.contact');
    }

    public function showProduct($id)
    {
        $product = Product::with(['category', 'brand'])->find($id);
        
        // Check if request is for Tailwind view
        if (request()->has('tailwind')) {
            return view('frontend.product-tailwind', compact('product'));
        }
        
        return view('home.product', ['product' => $product]);
    }
}
