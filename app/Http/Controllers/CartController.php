<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    public function addToCart(Request $request, $productId)
    {
        // Znajdź produkt
        $product = Product::find($productId);
        if (!$product) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Produkt nie znaleziony'], 404);
            }
            return redirect()->back()->with('error', 'Produkt nie znaleziony');
        }

        // Pobierz koszyk z sesji
        $cart = session()->get('cart', []);

        // Dodaj produkt do koszyka
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'product' => $product,
                'quantity' => 1,
            ];
        }

        // Oblicz sumę ilości i cenę całkowitą
        $totalQuantity = 0;
        $totalPrice = 0;
        
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
            $totalPrice += $item['product']->price * $item['quantity'];
        }

        // Zapisz koszyk z powrotem do sesji
        session()->put('cart', $cart);
        session()->save();

        // Odpowiedz w zależności od typu żądania
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Produkt dodany do koszyka', 
                'cart' => $cart, 
                'total_quantity' => $totalQuantity,
                'total_price' => number_format($totalPrice, 2)
            ]);
        }

        // Dla zwykłych żądań HTTP
        return redirect()->back()->with('success', 'Produkt został dodany do koszyka');
    }

    public function getCartContents(Request $request)
    {
        $cart = session()->get('cart', []);
        $totalQuantity = 0;
        $totalPrice = 0;
        
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
            $totalPrice += $item['product']->price * $item['quantity'];
        }
        
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total_quantity' => $totalQuantity,
            'total_price' => number_format($totalPrice, 2)
        ]);
    }

    public function cartView(Request $request)
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $quantity = 0;

        foreach ($cart as $item) {
            $total += $item['product']->price * $item['quantity'];
            $quantity += $item['quantity'];
        }

        // Użyj widoku cart.blade.php z prawidłowej ścieżki
        return view('frontend.cart.cart', compact('cart', 'total', 'quantity'));
    }

    protected function paginate($items, $perPage, $page, $options = [])
    {
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $page = $page ?: Paginator::resolveCurrentPage() ?: 1;

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }

    public function deleteFromCart(Request $request, $productId)
    {
        $cart = session()->get('cart', []);

        if (array_key_exists($productId, $cart)) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            } else {
                unset($cart[$productId]);
            }

            $totalQuantity = 0;
            $totalPrice = 0;
            
            foreach ($cart as $item) {
                $totalQuantity += $item['quantity'];
                $totalPrice += $item['product']->price * $item['quantity'];
            }

            session()->put('cart', $cart);
            session()->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produkt usunięty z koszyka', 
                    'cart' => $cart, 
                    'total_quantity' => $totalQuantity,
                    'total_price' => number_format($totalPrice, 2)
                ]);
            }

            return redirect()->back()->with('success', 'Produkt został usunięty z koszyka');
        } 
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Produkt nie został znaleziony w koszyku', 'cart' => $cart], 404);
        }

        return redirect()->back()->with('error', 'Produkt nie został znaleziony w koszyku');
    }
}
