<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use PHPUnit\Framework\Attributes\Test;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $product;
    protected $category;
    protected $brand;

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
        
        // Create category and brand
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create([
            'name' => 'Test Brand ' . uniqid()
        ]);
        
        // Create product
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);
    }

    #[Test]
    public function admin_can_view_products_list()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/products');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'price',
                    'category_id',
                    'brand_id'
                ]
            ]
        ]);
    }

    #[Test]
    public function admin_can_create_product()
    {
        $productData = [
            'name' => 'New Product',
            'description' => 'Product description',
            'price' => 150.00,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'stock' => 10,
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/products', $productData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    #[Test]
    public function admin_can_update_product()
    {
        $updateData = [
            'name' => 'Updated Product',
            'price' => 200.00
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/products/{$this->product->id}", $updateData);

        $response->assertStatus(200);
        $this->assertEquals('Updated Product', $this->product->fresh()->name);
    }

    #[Test]
    public function admin_can_delete_product()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/products/{$this->product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $this->product->id]);
    }

    #[Test]
    public function non_admin_cannot_access_products()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/products');

        $response->assertStatus(403);
    }
} 