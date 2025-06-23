<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $category;
    protected $brand;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $this->brand = Brand::factory()->create(['name' => 'Test Brand']);
    }

    public function test_it_can_fetch_products_list()
    {
        // Arrange
        Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'category',
                            'brand',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'current_page',
                    'per_page',
                    'total'
                ]);
    }

    public function test_it_can_fetch_single_product()
    {
        // Arrange
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        // Act
        $response = $this->getJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(200)
                ->assertJson([
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                ])
                ->assertJsonStructure([
                    'id',
                    'name',
                    'price',
                    'category' => ['id', 'name'],
                    'brand' => ['id', 'name'],
                ]);
    }

    public function test_it_returns_404_for_non_existent_product()
    {
        // Act
        $response = $this->getJson('/api/products/999');

        // Assert
        $response->assertStatus(404)
                ->assertJson([
                    'error' => 'Product not found'
                ]);
    }

    public function test_it_can_filter_products_by_category()
    {
        // Arrange
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        
        Product::factory()->count(3)->create(['category_id' => $category1->id]);
        Product::factory()->count(2)->create(['category_id' => $category2->id]);

        // Act
        $response = $this->getJson("/api/products?category_id={$category1->id}");

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(3, $data);
        
        foreach ($data as $product) {
            $this->assertEquals($category1->id, $product['category']['id']);
        }
    }

    public function test_it_can_search_products_by_name()
    {
        // Arrange
        Product::factory()->create([
            'name' => 'iPhone 15 Pro',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);
        
        Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        // Act
        $response = $this->getJson('/api/products?search=iPhone');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('iPhone', $data[0]['name']);
    }

    public function test_it_can_filter_products_by_price_range()
    {
        // Arrange
        Product::factory()->create([
            'price' => 100,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);
        
        Product::factory()->create([
            'price' => 500,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);
        
        Product::factory()->create([
            'price' => 1000,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        // Act
        $response = $this->getJson('/api/products?price_min=200&price_max=800');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(500, $data[0]['price']);
    }

    public function test_it_can_sort_products()
    {
        // Arrange - usuwamy wszystkie istniejące produkty z factory setup
        Product::where('category_id', $this->category->id)->delete();
        
        $productZ = Product::factory()->create([
            'name' => 'Z Product',
            'price' => 100,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);
        
        $productA = Product::factory()->create([
            'name' => 'A Product',
            'price' => 200,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        // Act - Sort by name ascending
        $response = $this->getJson('/api/products?sort_by=name&sort_direction=asc');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(2, count($data));
        
        // Znajdź nasze produkty w odpowiedzi
        $productNames = array_column($data, 'name');
        $aIndex = array_search('A Product', $productNames);
        $zIndex = array_search('Z Product', $productNames);
        
        $this->assertNotFalse($aIndex, 'Product A not found in response');
        $this->assertNotFalse($zIndex, 'Product Z not found in response');
        $this->assertLessThan($zIndex, $aIndex, 'Products not sorted correctly');
    }

    public function test_it_can_fetch_featured_products()
    {
        // Arrange - sprawdź czy kolumna featured istnieje
        $columns = \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnListing('products');
        if (!in_array('featured', $columns)) {
            $this->markTestSkipped('Featured column does not exist in products table');
        }
        
        Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'featured' => true,
        ]);
        
        Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'featured' => false,
        ]);

        // Act - używamy filtru featured_only
        $response = $this->getJson('/api/products?featured_only=1');

        // Assert
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'category',
                            'brand'
                        ]
                    ]
                ]);
        
        // Should return only featured products
        $data = $response->json('data');
        $this->assertCount(5, $data);
    }

    public function test_it_validates_pagination_parameters()
    {
        // Arrange
        Product::factory()->count(20)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        // Act
        $response = $this->getJson('/api/products?per_page=5');

        // Assert
        $response->assertStatus(200);
        $this->assertEquals(5, $response->json('per_page'));
        $this->assertCount(5, $response->json('data'));
    }

    public function test_it_handles_invalid_sort_field_gracefully()
    {
        // Arrange
        Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
        ]);

        // Act - Try to sort by invalid field
        $response = $this->getJson('/api/products?sort_by=invalid_field');

        // Assert
        $response->assertStatus(200); // Should not fail, just use default sorting
    }
} 