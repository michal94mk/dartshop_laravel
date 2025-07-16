<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\User as UserModel;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;

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
    }

    /** @test */
    public function admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'counts' => [
                'products',
                'users',
                'orders',
                'reviews'
            ],
            'recent_orders',
            'sales_data',
            'top_products',
            'categories_data'
        ]);
    }

    /** @test */
    public function admin_can_view_recent_orders()
    {
        // Create some orders
        Order::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'recent_orders' => [
                '*' => [
                    'id',
                    'total',
                    'status',
                    'created_at'
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_view_sales_data()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sales_data' => [
                '*' => [
                    'date',
                    'total_sales',
                    'order_count'
                ]
            ]
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }
} 