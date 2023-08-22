<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('home', compact('products'));
    }

    public function indexForRegularUsers(Request $request): View|JsonResponse
    {
        $filters = $request->query('filter');
        $query = Product::query();

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

            $products = $query->with('category')->get();

            $data = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category->name,
                ];
            });

            return response()->json([
                'data' => $data
            ]);
        }

        return view('frontend.categories.index', [
            'products' => $query->paginate(10),
            'categories' => Category::all()
        ]);
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
        $product = Product::find($id);
        return view('home.product', ['product' => $product]);
    }
}
