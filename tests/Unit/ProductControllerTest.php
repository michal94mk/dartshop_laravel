<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create admin role
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        
        $adminUser = User::factory()->create(['is_admin' => true]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
    }

    public function testProductCanBeCreated()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 19.99,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ];

        $response = $this->postJson('/api/admin/products', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data']);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 19.99,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);
    }

    public function testProductCanBeSuccessfullyUpdated()
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Product Name',
            'description' => 'Updated Description',
            'price' => 25.99,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
        ];

        $response = $this->putJson("/api/admin/products/{$product->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'description' => 'Updated Description',
            'price' => 25.99,
        ]);
    }

    public function testCanDeleteProduct()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
