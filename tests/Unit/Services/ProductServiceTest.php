<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected ProductService $productService;
    protected Category $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear database and cache before each test
        Product::query()->delete();
        Category::query()->delete();
        Brand::query()->delete();
        Cache::flush();
        
        $this->productService = new ProductService();
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create();
    }

    protected function tearDown(): void
    {
        // Clear cache after each test
        Cache::flush();
        
        parent::tearDown();
    }

    #[Test]
    public function it_can_get_products_with_pagination()
    {
        // Create products
        Product::factory()->count(15)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);

        // Create request
        $request = new Request(['per_page' => 10]);

        // Get products
        $products = $this->productService->getProducts($request);

        $this->assertEquals(10, $products->perPage());
        $this->assertEquals(15, $products->total());
        $this->assertCount(10, $products->items());
    }

    #[Test]
    public function it_can_filter_products_by_category()
    {
        // Create products in different categories
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Product::factory()->count(3)->create(['category_id' => $category1->id]);
        Product::factory()->count(5)->create(['category_id' => $category2->id]);

        // Create request with category filter
        $request = new Request(['category_id' => $category1->id]);

        // Get products
        $products = $this->productService->getProducts($request);

        $this->assertEquals(3, $products->total());
        foreach ($products->items() as $product) {
            $this->assertEquals($category1->id, $product->category_id);
        }
    }

    #[Test]
    public function it_can_filter_products_by_brand()
    {
        // Create products with different brands
        $brand1 = Brand::factory()->create();
        $brand2 = Brand::factory()->create();

        Product::factory()->count(2)->create(['brand_id' => $brand1->id]);
        Product::factory()->count(4)->create(['brand_id' => $brand2->id]);

        // Create request with brand filter
        $request = new Request(['brand_id' => $brand1->id]);

        // Get products
        $products = $this->productService->getProducts($request);

        $this->assertEquals(2, $products->total());
        foreach ($products->items() as $product) {
            $this->assertEquals($brand1->id, $product->brand_id);
        }
    }

    #[Test]
    public function it_can_search_products_by_name()
    {
        // Create products with specific names
        Product::factory()->create(['name' => 'Test Dart Set']);
        Product::factory()->create(['name' => 'Another Product']);
        Product::factory()->create(['name' => 'Dart Board']);

        // Create request with search
        $request = new Request(['search' => 'Dart']);

        // Get products
        $products = $this->productService->getProducts($request);

        $this->assertEquals(2, $products->total());
        foreach ($products->items() as $product) {
            $this->assertStringContainsString('Dart', $product->name);
        }
    }

    #[Test]
    public function it_can_filter_products_by_price_range()
    {
        // Create products with different prices
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 100.00]);
        Product::factory()->create(['price' => 150.00]);
        Product::factory()->create(['price' => 200.00]);

        // Create request with price range
        $request = new Request([
            'price_min' => 75.00,
            'price_max' => 175.00
        ]);

        // Get products
        $products = $this->productService->getProducts($request);

        $this->assertEquals(2, $products->total());
        foreach ($products->items() as $product) {
            $this->assertGreaterThanOrEqual(75.00, $product->price);
            $this->assertLessThanOrEqual(175.00, $product->price);
        }
    }

    #[Test]
    public function it_can_sort_products_by_price_ascending()
    {
        // Create products with different prices
        Product::factory()->create(['price' => 200.00]);
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 150.00]);

        // Create request with sorting
        $request = new Request([
            'sort_by' => 'price',
            'sort_direction' => 'asc'
        ]);

        // Get products
        $products = $this->productService->getProducts($request);

        $prices = collect($products->items())->pluck('price')->map(fn($p) => (float)$p)->toArray();
        $this->assertEquals([50.00, 150.00, 200.00], $prices);
    }

    #[Test]
    public function it_can_sort_products_by_price_descending()
    {
        // Create products with different prices
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 200.00]);
        Product::factory()->create(['price' => 150.00]);

        // Create request with sorting
        $request = new Request([
            'sort_by' => 'price',
            'sort_direction' => 'desc'
        ]);

        // Get products
        $products = $this->productService->getProducts($request);

        $prices = collect($products->items())->pluck('price')->map(fn($p) => (float)$p)->toArray();
        $this->assertEquals([200.00, 150.00, 50.00], $prices);
    }

    #[Test]
    public function it_can_get_single_product()
    {
        // Create product
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00
        ]);

        // Get product
        $result = $this->productService->getProduct($product->id);

        $this->assertNotNull($result);
        $this->assertEquals($product->id, $result->id);
        $this->assertEquals('Test Product', $result->name);
        $this->assertEquals(100.00, $result->price);
    }

    #[Test]
    public function it_throws_exception_for_non_existent_product()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Try to get non-existent product
        $this->productService->getProduct(999);
    }

    #[Test]
    public function it_can_get_latest_products()
    {
        // Create products
        Product::factory()->count(15)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);

        // Get latest products
        $products = $this->productService->getLatestProducts(5);

        $this->assertCount(5, $products);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $products);
    }

    #[Test]
    public function it_can_add_promotion_info_to_product()
    {
        // Create product
        $product = Product::factory()->create([
            'price' => 100.00
        ]);

        // Create active promotion
        $promotion = Promotion::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay()
        ]);

        // Attach promotion to product
        $product->promotions()->attach($promotion->id);

        // Add promotion info
        $result = $this->productService->addPromotionInfo($product);

        $this->assertNotNull($result);
        $this->assertEquals(80.00, $result->promotion_price); // 100 - 20%
        $this->assertArrayHasKey('discount_value', $result->promotion);
    }

    #[Test]
    public function it_handles_products_without_promotions()
    {
        // Create product without promotions
        $product = Product::factory()->create([
            'price' => 100.00
        ]);

        // Add promotion info
        $result = $this->productService->addPromotionInfo($product);

        $this->assertNotNull($result);
        $this->assertEquals(100.00, $result->price);
        $this->assertNull($result->promotional_price ?? null);
    }

    #[Test]
    public function it_can_get_filters_metadata()
    {
        // Create categories and brands
        Category::factory()->count(3)->create();
        Brand::factory()->count(2)->create();

        // Get filters metadata
        $metadata = $this->productService->getFiltersMetadata();

        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('categories', $metadata);
        $this->assertArrayHasKey('brands', $metadata);
        $this->assertArrayHasKey('price_range', $metadata);
    }

    #[Test]
    public function it_respects_pagination_limits()
    {
        // Create products
        Product::factory()->count(10)->create();

        // Test minimum limit
        $request = new Request(['per_page' => 0]);
        $products = $this->productService->getProducts($request);
        $this->assertEquals(1, $products->perPage());

        // Test maximum limit
        $request = new Request(['per_page' => 100]);
        $products = $this->productService->getProducts($request);
        $this->assertEquals(50, $products->perPage());
    }

    #[Test]
    public function it_caches_products_list()
    {
        // Create products
        Product::factory()->count(5)->create();

        // Clear cache
        Cache::flush();

        // First request (cache miss)
        $request1 = new Request(['per_page' => 10]);
        $products1 = $this->productService->getProducts($request1);

        // Second request (cache hit)
        $request2 = new Request(['per_page' => 10]);
        $products2 = $this->productService->getProducts($request2);

        $this->assertEquals($products1->total(), $products2->total());
        $this->assertEquals($products1->count(), $products2->count());
    }
} 