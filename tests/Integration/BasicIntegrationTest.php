<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class BasicIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Category $category;
    protected Brand $brand;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create([
            'name' => 'Test Brand ' . uniqid()
        ]);
        
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 100.00
        ]);
    }

    #[Test]
    public function test_basic_product_api_integration()
    {
        // Test products list endpoint
        $productsResponse = $this->getJson('/api/products');
        $productsResponse->assertStatus(200);
        $productsData = $productsResponse->json('data');
        $this->assertIsArray($productsData);
        $this->assertGreaterThan(0, count($productsData));

        // Test single product endpoint
        $productResponse = $this->getJson("/api/products/{$this->product->id}");
        $productResponse->assertStatus(200);
        $productData = $productResponse->json('data');
        $this->assertEquals($this->product->id, $productData['id']);
        $this->assertEquals($this->product->name, $productData['name']);
        $this->assertEquals($this->product->price, $productData['price']);
    }

    #[Test]
    public function test_basic_category_api_integration()
    {
        // Test categories list endpoint
        $categoriesResponse = $this->getJson('/api/categories');
        $categoriesResponse->assertStatus(200);
        $categoriesData = $categoriesResponse->json('data');
        $this->assertIsArray($categoriesData);
        $this->assertGreaterThan(0, count($categoriesData));

        // Test single category endpoint
        $categoryResponse = $this->getJson("/api/categories/{$this->category->id}");
        $categoryResponse->assertStatus(200);
        $categoryData = $categoryResponse->json('data');
        $this->assertEquals($this->category->id, $categoryData['id']);
    }

    #[Test]
    public function test_basic_brand_api_integration()
    {
        // Test brands list endpoint (admin only)
        $admin = User::factory()->create(['is_admin' => true]);
        $brandsResponse = $this->actingAs($admin)
            ->getJson('/api/admin/brands');
        $brandsResponse->assertStatus(200);
        $brandsData = $brandsResponse->json('data');
        $this->assertIsArray($brandsData);
        $this->assertGreaterThan(0, count($brandsData));
    }

    #[Test]
    public function test_product_filtering_integration()
    {
        // Create additional products for filtering
        Product::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 50.00
        ]);

        // Test filtering by category
        $categoryFilterResponse = $this->getJson("/api/products?category_id={$this->category->id}");
        $categoryFilterResponse->assertStatus(200);
        $categoryProducts = $categoryFilterResponse->json('data');
        $this->assertGreaterThan(0, count($categoryProducts));

        // Test filtering by brand (if supported)
        $brandFilterResponse = $this->getJson("/api/products?brand_id={$this->brand->id}");
        $brandFilterResponse->assertStatus(200);
        $brandProducts = $brandFilterResponse->json('data');
        $this->assertGreaterThan(0, count($brandProducts));
    }

    #[Test]
    public function test_user_orders_integration()
    {
        // Create multiple orders for user
        Order::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing
        ]);

        // Test user orders endpoint
        $userOrdersResponse = $this->actingAs($this->user)
            ->getJson('/api/orders/my-orders');

        $userOrdersResponse->assertStatus(200);
        $userOrdersData = $userOrdersResponse->json('data');
        $this->assertIsArray($userOrdersData);
        $this->assertGreaterThanOrEqual(3, count($userOrdersData));
    }

    #[Test]
    public function test_admin_dashboard_integration()
    {
        // Create admin user
        $admin = User::factory()->create([
            'is_admin' => true
        ]);

        // Create some test data
        Product::factory()->count(5)->create();
        Order::factory()->count(3)->create();

        // Test admin dashboard endpoint
        $dashboardResponse = $this->actingAs($admin)
            ->getJson('/api/admin/dashboard');

        $dashboardResponse->assertStatus(200);
        $dashboardData = $dashboardResponse->json('data');

        // Verify dashboard structure
        $this->assertArrayHasKey('counts', $dashboardData);
        $this->assertArrayHasKey('products', $dashboardData['counts']);
        $this->assertArrayHasKey('orders', $dashboardData['counts']);
        $this->assertArrayHasKey('users', $dashboardData['counts']);

        // Verify counts are reasonable
        $this->assertGreaterThan(0, $dashboardData['counts']['products']);
        $this->assertGreaterThan(0, $dashboardData['counts']['orders']);
    }

    #[Test]
    public function test_api_response_structure_consistency()
    {
        // Test that all API responses follow consistent structure
        $endpoints = [
            '/api/products',
            '/api/categories'
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            $response->assertStatus(200);

            $responseData = $response->json();
            
            // Verify consistent response structure
            $this->assertArrayHasKey('success', $responseData);
            $this->assertArrayHasKey('data', $responseData);
            $this->assertTrue($responseData['success']);
            $this->assertIsArray($responseData['data']);
        }
    }

    #[Test]
    public function test_error_handling_integration()
    {
        // Test 404 for non-existent product
        $nonExistentResponse = $this->getJson('/api/products/99999');
        $nonExistentResponse->assertStatus(404);

        // Test 404 for non-existent category
        $nonExistentCategoryResponse = $this->getJson('/api/categories/99999');
        $nonExistentCategoryResponse->assertStatus(404);

        // Test unauthorized access to admin endpoints
        $adminResponse = $this->actingAs($this->user) // Regular user, not admin
            ->getJson('/api/admin/dashboard');
        $adminResponse->assertStatus(403);
    }
} 