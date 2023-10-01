<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Session;

class CartControllerTest extends TestCase
{
    //use RefreshDatabase;

    public function testAddToCart()
    {
        $product = Product::factory()->create();
        $response = $this->post("/cart/add/{$product->id}");
        $response->assertStatus(200);
        $cart = session()->get('cart', []);
        $this->assertArrayHasKey($product->id, $cart);
        $this->assertEquals($product->id, $cart[$product->id]['product']->id);
        $this->assertEquals(1, $cart[$product->id]['quantity']);
    }

    public function testGetCartContents()
    {
        $response = $this->get('/cart/contents');
        $response->assertStatus(200);
    }

    public function testCartView()
    {
        $response = $this->get('/cart/view');
        $response->assertStatus(200);
    }

    public function testDeleteFromCart()
    {
        $product = Product::factory()->create();
        session()->put('cart', [$product->id => ['quantity' => 1]]);
        $response = $this->post(route('cart.delete', ['product' => $product->id]));
        $response->assertStatus(200);
        $cart = session()->get('cart', []);
        $this->assertArrayNotHasKey($product->id, $cart);
    }
}
