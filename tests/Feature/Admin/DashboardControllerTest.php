<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use PHPUnit\Framework\Attributes\Test;

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

    #[Test]
    public function admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
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
            ],
            'message'
        ]);
    }

    #[Test]
    public function admin_can_view_recent_orders()
    {
        // Create some orders
        Order::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'recent_orders' => [
                    '*' => [
                        'id',
                        'total',
                        'status',
                        'created_at'
                    ]
                ]
            ],
            'message'
        ]);
    }

    #[Test]
    public function admin_can_view_sales_data()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'sales_data' => [
                    '*' => [
                        'date',
                        'total_sales',
                        'order_count'
                    ]
                ]
            ],
            'message'
        ]);
    }

    #[Test]
    public function non_admin_cannot_access_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }
} 