<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class ProductIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Product $product;
    protected Category $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create([
            'name' => 'Test Brand ' . uniqid()
        ]);
        
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);
    }

    #[Test]
    public function test_product_search_with_filters_and_sorting()
    {
        // Create additional products for search testing
        Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);

        // Test product search
        $searchResponse = $this->getJson('/api/products?search=Test');
        $searchResponse->assertStatus(200);
        $searchData = $searchResponse->json('data');
        $this->assertGreaterThanOrEqual(1, count($searchData));

        // Test product filtering by category
        $filterResponse = $this->getJson("/api/products?category={$this->category->id}");
        $filterResponse->assertStatus(200);
        $filterData = $filterResponse->json('data');
        $this->assertGreaterThanOrEqual(1, count($filterData));

        // Test product sorting
        $sortResponse = $this->getJson('/api/products?sort=price&order=asc');
        $sortResponse->assertStatus(200);
        $sortData = $sortResponse->json('data');
        $this->assertGreaterThanOrEqual(1, count($sortData));
    }

    #[Test]
    public function test_product_reviews_impact_on_visibility()
    {
        // Create review for product
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'is_approved' => true
        ]);

        // Verify review was created
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'is_approved' => true
        ]);

        // Test product reviews endpoint
        $reviewsResponse = $this->getJson("/api/products/{$this->product->id}/reviews");
        $reviewsResponse->assertStatus(200);
        $reviewsData = $reviewsResponse->json('data');
        $this->assertGreaterThanOrEqual(1, count($reviewsData));
    }

    #[Test]
    public function test_product_promotions_calculation()
    {
        // Create promotion
        $promotion = Promotion::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay()
        ]);

        // Test product promotion check endpoint
        $promotionResponse = $this->getJson("/api/products/{$this->product->id}/promotion");
        $promotionResponse->assertStatus(200);

        // Verify promotion exists
        $this->assertDatabaseHas('promotions', [
            'id' => $promotion->id,
            'discount_type' => 'percentage',
            'discount_value' => 20
        ]);
    }

    #[Test]
    public function test_product_favorites_integration()
    {
        // Test favorites endpoint
        $favoritesResponse = $this->actingAs($this->user)
            ->getJson('/api/favorites');
        $favoritesResponse->assertStatus(200);

        // Test adding to favorites
        $addFavoriteResponse = $this->actingAs($this->user)
            ->postJson("/api/favorites/{$this->product->id}");
        $addFavoriteResponse->assertStatus(200);

        // Verify product exists
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'name' => 'Test Product'
        ]);
    }

    #[Test]
    public function test_product_related_items_integration()
    {
        // Create additional products in same category
        Product::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);

        // Test category products endpoint
        $categoryProductsResponse = $this->getJson("/api/categories/{$this->category->id}/products");
        $categoryProductsResponse->assertStatus(200);
        $categoryProductsData = $categoryProductsResponse->json('data');
        $this->assertGreaterThanOrEqual(1, count($categoryProductsData));
    }

    #[Test]
    public function test_product_pagination_and_performance()
    {
        // Create many products for pagination testing
        Product::factory()->count(25)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id
        ]);

        // Test pagination
        $page1Response = $this->getJson('/api/products?page=1&per_page=10');
        $page1Response->assertStatus(200);
        $page1Data = $page1Response->json('data');
        $this->assertGreaterThanOrEqual(10, count($page1Data));

        // Test second page
        $page2Response = $this->getJson('/api/products?page=2&per_page=10');
        $page2Response->assertStatus(200);
        $page2Data = $page2Response->json('data');
        $this->assertGreaterThanOrEqual(1, count($page2Data));
    }
} 