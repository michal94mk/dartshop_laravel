<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CartService;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class CartServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected CartService $cartService;
    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cartService = new CartService();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00
        ]);
    }

    protected function tearDown(): void
    {
        // Clear cache after each test
        Cache::flush();
        
        parent::tearDown();
    }

    #[Test]
    public function it_can_get_cart_items_for_authenticated_user()
    {
        // Create cart item
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Authenticate user
        Auth::login($this->user);

        // Get cart items
        $cartItems = $this->cartService->getCartItems();

        $this->assertCount(1, $cartItems);
        $this->assertEquals($this->product->id, $cartItems->first()->product_id);
        $this->assertEquals(2, $cartItems->first()->quantity);
    }

    #[Test]
    public function it_can_get_guest_cart_items()
    {
        // Set guest cart in session
        Session::put('cart', [
            [
                'product_id' => $this->product->id,
                'quantity' => 3
            ]
        ]);

        // Get guest cart items
        $cartItems = $this->cartService->getGuestCartItems();

        $this->assertCount(1, $cartItems);
        $this->assertEquals($this->product->id, $cartItems->first()->product_id);
        $this->assertEquals(3, $cartItems->first()->quantity);
    }

    #[Test]
    public function it_can_add_item_to_authenticated_user_cart()
    {
        Auth::login($this->user);

        // Add item to cart
        $cartItem = $this->cartService->addToCart($this->product, 2);

        $this->assertNotNull($cartItem);
        $this->assertEquals($this->user->id, $cartItem->user_id);
        $this->assertEquals($this->product->id, $cartItem->product_id);
        $this->assertEquals(2, $cartItem->quantity);

        // Verify in database
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    #[Test]
    public function it_can_add_item_to_guest_cart()
    {
        // Add item to guest cart
        $this->cartService->addToCart($this->product, 3);

        // Verify in session
        $cart = Session::get('cart', []);
        $this->assertCount(1, $cart);
        $this->assertEquals($this->product->id, $cart[0]['product_id']);
        $this->assertEquals(3, $cart[0]['quantity']);
    }

    #[Test]
    public function it_can_update_quantity_for_authenticated_user()
    {
        Auth::login($this->user);

        // Create cart item
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        // Update quantity
        $cartItem = $this->cartService->updateQuantity($this->product->id, 5);

        $this->assertNotNull($cartItem);
        $this->assertEquals(5, $cartItem->quantity);

        // Verify in database
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);
    }

    #[Test]
    public function it_can_update_quantity_for_guest_user()
    {
        // Set guest cart in session
        Session::put('cart', [
            [
                'product_id' => $this->product->id,
                'quantity' => 1
            ]
        ]);

        // Update quantity
        $this->cartService->updateQuantity($this->product->id, 4);

        // Verify in session
        $cart = Session::get('cart', []);
        $this->assertCount(1, $cart);
        $this->assertEquals(4, $cart[0]['quantity']);
    }

    #[Test]
    public function it_can_remove_item_from_authenticated_user_cart()
    {
        Auth::login($this->user);

        // Create cart item
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        // Remove item
        $result = $this->cartService->removeFromCart($this->product->id);

        $this->assertTrue($result);

        // Verify removed from database
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    #[Test]
    public function it_can_remove_item_from_guest_cart()
    {
        // Set guest cart in session
        Session::put('cart', [
            [
                'product_id' => $this->product->id,
                'quantity' => 1
            ]
        ]);

        // Remove item
        $result = $this->cartService->removeFromCart($this->product->id);

        $this->assertTrue($result);

        // Verify removed from session
        $cart = Session::get('cart', []);
        $this->assertEmpty($cart);
    }

    #[Test]
    public function it_can_clear_authenticated_user_cart()
    {
        Auth::login($this->user);

        // Create cart items
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        // Clear cart
        $this->cartService->clearCart();

        // Verify cart is empty
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $this->user->id
        ]);
    }

    #[Test]
    public function it_can_clear_guest_cart()
    {
        // Set guest cart in session
        Session::put('cart', [
            [
                'product_id' => $this->product->id,
                'quantity' => 1
            ]
        ]);

        // Clear cart
        $this->cartService->clearGuestCart();

        // Verify session is empty
        $cart = Session::get('cart', []);
        $this->assertEmpty($cart);
    }

    #[Test]
    public function it_can_calculate_cart_total_for_authenticated_user()
    {
        Auth::login($this->user);

        // Create cart items
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Calculate total
        $total = $this->cartService->getCartTotal();

        $this->assertEquals(200.00, $total); // 2 * 100.00
    }

    #[Test]
    public function it_can_calculate_cart_total_for_guest_user()
    {
        // Set guest cart in session
        Session::put('cart', [
            [
                'product_id' => $this->product->id,
                'quantity' => 3
            ]
        ]);

        // Calculate total
        $total = $this->cartService->getCartTotal();

        $this->assertEquals(300.00, $total); // 3 * 100.00
    }

    #[Test]
    public function it_can_get_cart_items_count_for_authenticated_user()
    {
        Auth::login($this->user);

        // Create cart items
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Get count
        $count = $this->cartService->getCartItemsCount();

        $this->assertEquals(2, $count); // Sum of quantities
    }

    #[Test]
    public function it_can_get_cart_items_count_for_guest_user()
    {
        // Set guest cart in session
        Session::put('cart', [
            [
                'product_id' => $this->product->id,
                'quantity' => 2
            ]
        ]);

        // Get count
        $count = $this->cartService->getCartItemsCount();

        $this->assertEquals(2, $count); // Sum of quantities
    }

    #[Test]
    public function it_can_migrate_session_cart_to_database()
    {
        // Set guest cart in session
        Session::put('cart', [
            [
                'product_id' => $this->product->id,
                'quantity' => 2
            ]
        ]);

        // Authenticate user
        Auth::login($this->user);

        // Migrate cart
        $this->cartService->migrateSessionCartToDatabase();

        // Verify cart item was created in database
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Verify session cart was cleared
        $cart = Session::get('cart', []);
        $this->assertEmpty($cart);
    }
} 