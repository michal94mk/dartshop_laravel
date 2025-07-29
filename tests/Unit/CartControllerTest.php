<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testAddToCart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $product = Product::factory()->create();
        $response = $this->postJson("/api/cart", [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        $response->assertStatus(201);
    }

    public function testGetCartContents()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->getJson('/api/cart');
        $response->assertStatus(200);
    }

    public function testCartView()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testDeleteFromCart()
    {
        // Najpierw dodaj produkt do koszyka
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $cartItem = $user->cartItems()->create([
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        
        $response = $this->deleteJson("/api/cart/{$cartItem->id}");
        $response->assertStatus(200);
    }
}
