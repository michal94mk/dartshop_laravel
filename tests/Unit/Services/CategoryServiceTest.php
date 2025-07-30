<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CategoryService;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear database and cache before each test
        Category::query()->delete();
        Cache::flush();
        
        $this->categoryService = new CategoryService();
    }

    protected function tearDown(): void
    {
        // Clear cache after each test
        Cache::flush();
        
        parent::tearDown();
    }

    #[Test]
    public function it_can_get_categories_list()
    {
        // Create categories with unique names
        Category::factory()->create(['name' => 'Category 1']);
        Category::factory()->create(['name' => 'Category 2']);
        Category::factory()->create(['name' => 'Category 3']);

        // Create request
        $request = new Request();

        // Get categories
        $categories = $this->categoryService->getCategories($request);

        // Check that our specific categories exist
        $names = $categories->pluck('name')->toArray();
        $this->assertContains('Category 1', $names);
        $this->assertContains('Category 2', $names);
        $this->assertContains('Category 3', $names);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $categories);
    }

    #[Test]
    public function it_can_get_categories_with_products_only()
    {
        // Create categories with and without products
        $categoryWithProducts = Category::factory()->create(['name' => 'Category With Products']);
        $categoryWithoutProducts = Category::factory()->create(['name' => 'Category Without Products']);

        // Create products for one category
        Product::factory()->count(3)->create(['category_id' => $categoryWithProducts->id]);

        // Create request with filter
        $request = new Request(['with_products_only' => true]);

        // Get categories
        $categories = $this->categoryService->getCategories($request);

        $this->assertCount(1, $categories);
        $this->assertEquals($categoryWithProducts->id, $categories->first()->id);
    }

    #[Test]
    public function it_can_transform_category_data()
    {
        // Create category with products
        $category = Category::factory()->create(['name' => 'Test Category']);
        Product::factory()->count(2)->create(['category_id' => $category->id]);

        // Transform category data
        $transformed = $this->categoryService->transformCategoryData($category);

        $this->assertIsArray($transformed);
        $this->assertEquals($category->id, $transformed['id']);
        $this->assertEquals('Test Category', $transformed['name']);
        $this->assertArrayHasKey('products_count', $transformed);
        $this->assertTrue($transformed['is_active']);
        $this->assertArrayHasKey('preview_products', $transformed);
        $this->assertArrayHasKey('created_at', $transformed);
        $this->assertArrayHasKey('updated_at', $transformed);
    }

    #[Test]
    public function it_can_get_single_category()
    {
        // Create category
        $category = Category::factory()->create(['name' => 'Test Category']);

        // Get category
        $result = $this->categoryService->getCategory($category->id);

        $this->assertIsArray($result);
        $this->assertEquals($category->id, $result['id']);
        $this->assertEquals('Test Category', $result['name']);
        $this->assertArrayHasKey('products_count', $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
    }

    #[Test]
    public function it_throws_exception_for_non_existent_category()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Try to get non-existent category
        $this->categoryService->getCategory(999);
    }

    #[Test]
    public function it_can_get_category_products()
    {
        // Create category
        $category = Category::factory()->create();

        // Create products for category
        Product::factory()->count(10)->create(['category_id' => $category->id]);

        // Create request
        $request = new Request(['per_page' => 5]);

        // Get category products
        $products = $this->categoryService->getCategoryProducts($category->id, $request);

        $this->assertEquals(5, $products->perPage());
        $this->assertEquals(10, $products->total());
        $this->assertCount(5, $products->items());

        // Verify all products belong to the category
        foreach ($products->items() as $product) {
            $this->assertEquals($category->id, $product->category_id);
        }
    }

    #[Test]
    public function it_can_filter_category_products_by_brand()
    {
        // Create category and brands
        $category = Category::factory()->create();
        $brand1 = \App\Models\Brand::factory()->create();
        $brand2 = \App\Models\Brand::factory()->create();

        // Create products with different brands
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'brand_id' => $brand1->id
        ]);
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'brand_id' => $brand2->id
        ]);

        // Create request with brand filter
        $request = new Request(['brand_id' => $brand1->id]);

        // Get category products
        $products = $this->categoryService->getCategoryProducts($category->id, $request);

        $this->assertEquals(3, $products->total());
        foreach ($products->items() as $product) {
            $this->assertEquals($brand1->id, $product->brand_id);
        }
    }

    #[Test]
    public function it_can_search_category_products()
    {
        // Create category
        $category = Category::factory()->create();

        // Create products with specific names
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Dart Set'
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Another Product'
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Dart Board'
        ]);

        // Create request with search
        $request = new Request(['search' => 'Dart']);

        // Get category products
        $products = $this->categoryService->getCategoryProducts($category->id, $request);

        $this->assertEquals(2, $products->total());
        foreach ($products->items() as $product) {
            $this->assertStringContainsString('Dart', $product->name);
        }
    }

    #[Test]
    public function it_can_sort_category_products_by_price()
    {
        // Create category
        $category = Category::factory()->create();

        // Create products with different prices
        Product::factory()->create([
            'category_id' => $category->id,
            'price' => 200.00
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'price' => 50.00
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'price' => 150.00
        ]);

        // Create request with sorting
        $request = new Request([
            'sort_by' => 'price',
            'sort_direction' => 'asc'
        ]);

        // Get category products
        $products = $this->categoryService->getCategoryProducts($category->id, $request);

        $prices = collect($products->items())->pluck('price')->map(fn($p) => (float)$p)->toArray();
        $this->assertEquals([50.00, 150.00, 200.00], $prices);
    }

    #[Test]
    public function it_can_get_category_statistics()
    {
        // Create categories with different product counts
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);

        Product::factory()->count(5)->create(['category_id' => $category1->id]);
        Product::factory()->count(3)->create(['category_id' => $category2->id]);

        // Get statistics
        $statistics = $this->categoryService->getStatistics();

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('total_categories', $statistics);
        $this->assertArrayHasKey('categories_with_products', $statistics);
        $this->assertArrayHasKey('top_categories', $statistics);
        $this->assertEquals(2, $statistics['total_categories']);
        $this->assertEquals(2, $statistics['categories_with_products']);
    }

    #[Test]
    public function it_caches_categories_list()
    {
        // Create categories with unique names
        Category::factory()->create(['name' => 'Cache Category 1']);
        Category::factory()->create(['name' => 'Cache Category 2']);

        // Clear cache
        Cache::flush();

        // First request (cache miss)
        $request1 = new Request();
        $categories1 = $this->categoryService->getCategories($request1);

        // Second request (cache hit)
        $request2 = new Request();
        $categories2 = $this->categoryService->getCategories($request2);

        $this->assertEquals($categories1->count(), $categories2->count());
    }

    #[Test]
    public function it_caches_category_detail()
    {
        // Create category
        $category = Category::factory()->create(['name' => 'Cache Detail Category']);

        // Clear cache
        Cache::flush();

        // First request (cache miss)
        $result1 = $this->categoryService->getCategory($category->id);

        // Second request (cache hit)
        $result2 = $this->categoryService->getCategory($category->id);

        $this->assertEquals($result1['id'], $result2['id']);
        $this->assertEquals($result1['name'], $result2['name']);
    }

    #[Test]
    public function it_handles_empty_category_products()
    {
        // Create category without products
        $category = Category::factory()->create(['name' => 'Empty Category']);

        // Create request
        $request = new Request();

        // Get category products
        $products = $this->categoryService->getCategoryProducts($category->id, $request);

        $this->assertEquals(0, $products->total());
        $this->assertCount(0, $products->items());
    }
} 