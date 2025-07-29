<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use PHPUnit\Framework\Attributes\Test;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $order;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true
        ]);
        
        // Create regular user
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'is_admin' => false
        ]);
        
        // Create product
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00
        ]);
        
        // Create order
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total' => 100.00
        ]);
        
        // Create order item
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'product_name' => 'Test Product',
            'product_price' => 100.00,
            'quantity' => 1,
            'total_price' => 100.00
        ]);
    }

    #[Test]
    public function admin_can_view_orders_list()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'message'
        ]);
        
        // Check that data is an array or paginator
        $data = $response->json('data');
        $this->assertIsArray($data);
    }

    #[Test]
    public function non_admin_cannot_view_orders_list()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/orders');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_single_order()
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/orders/{$this->order->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number
            ]
        ]);
    }

    #[Test]
    public function admin_can_update_order_status()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/orders/{$this->order->id}/status", [
                'status' => 'processing'
            ]);

        $response->assertStatus(200);
        $this->assertEquals('processing', $this->order->fresh()->status->value);
    }

    #[Test]
    public function admin_cannot_update_order_with_invalid_status()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/orders/{$this->order->id}/status", [
                'status' => 'invalid_status'
            ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function admin_can_delete_order()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/orders/{$this->order->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('orders', ['id' => $this->order->id]);
    }
}
