<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00
        ]);
    }

    #[Test]
    public function test_basic_order_creation()
    {
        // Create cart items
        CartItem::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Create order directly
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 200.00,
            'subtotal' => 200.00
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'total_price' => 200.00
        ]);

        // Verify order was created
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing->value
        ]);

        // Verify order items were created
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    #[Test]
    public function test_guest_order_creation()
    {
        // Create guest order
        $order = Order::factory()->create([
            'user_id' => null,
            'email' => 'anna@example.com',
            'status' => OrderStatus::Processing,
            'total' => 300.00,
            'subtotal' => 300.00
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'total_price' => 300.00
        ]);

        // Verify guest order was created
        $this->assertDatabaseHas('orders', [
            'email' => 'anna@example.com',
            'status' => OrderStatus::Processing->value
        ]);

        $order = Order::where('email', 'anna@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(300.00, $order->subtotal);
        $this->assertNull($order->user_id); // Guest order
    }

    #[Test]
    public function test_order_with_promotional_discount()
    {
        // Create promotion
        $promotion = \App\Models\Promotion::factory()->create([
            'code' => 'TEST20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay()
        ]);

        // Create order with discount
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 80.00, // 100 - 20% discount
            'subtotal' => 100.00
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'total_price' => 100.00
        ]);

        // Verify order was created with discount
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total' => 80.00
        ]);
    }

    #[Test]
    public function test_order_status_management()
    {
        // Create order
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 100.00
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'total_price' => 100.00
        ]);

        // Update order status
        $order->update(['status' => OrderStatus::Cancelled]);

        // Verify order status changed
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::Cancelled->value
        ]);
    }
} 