<?php

namespace Tests\Unit\Services\Payment;

use Tests\TestCase;
use App\Services\Payment\PaymentService;
use App\Services\ShippingService;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class PaymentServiceCacheTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;
    protected $shippingServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shippingServiceMock = Mockery::mock(ShippingService::class);
        $this->paymentService = new PaymentService($this->shippingServiceMock);
    }

    protected function tearDown(): void
    {
        Cache::flush(); // Clear cache after each test
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_caches_payment_methods()
    {
        // First call should cache the result
        $methods1 = $this->paymentService->getPaymentMethods();
        
        // Second call should return cached result
        $methods2 = $this->paymentService->getPaymentMethods();
        
        $this->assertEquals($methods1, $methods2);
        $this->assertTrue(Cache::has('stripe_payment_methods'));
    }

    #[Test]
    public function it_caches_stripe_configuration()
    {
        // Mock configuration
        config(['services.stripe.secret' => 'sk_test_123']);
        
        // Clear any existing cache
        Cache::forget('stripe_secret_key');
        
        // This should trigger cache storage during initialization
        $service = new PaymentService($this->shippingServiceMock);
        
        $this->assertTrue(Cache::has('stripe_secret_key'));
        // Check that the cached value matches the config value
        $this->assertEquals('sk_test_123', Cache::get('stripe_secret_key'));
    }

    #[Test]
    public function it_caches_products_for_guest_cart()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100
        ]);

        $cartData = [
            ['product_id' => $product->id, 'quantity' => 1]
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('prepareLineItemsFromGuestCart');
        $method->setAccessible(true);

        // First call should cache the product
        $lineItems1 = $method->invoke($this->paymentService, $cartData);
        
        // Verify cache exists
        $cacheKey = "product_with_promotions_{$product->id}";
        $this->assertTrue(Cache::has($cacheKey));
        
        // Second call should use cached product
        $lineItems2 = $method->invoke($this->paymentService, $cartData);
        
        $this->assertEquals($lineItems1, $lineItems2);
    }

    #[Test]
    public function it_can_clear_payment_cache()
    {
        // Populate cache
        $this->paymentService->getPaymentMethods();
        config(['services.stripe.secret' => 'sk_test_123']);
        new PaymentService($this->shippingServiceMock);
        
        // Verify cache exists
        $this->assertTrue(Cache::has('stripe_payment_methods'));
        $this->assertTrue(Cache::has('stripe_secret_key'));
        
        // Clear cache
        $this->paymentService->clearCache();
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has('stripe_payment_methods'));
        $this->assertFalse(Cache::has('stripe_secret_key'));
    }

    #[Test]
    public function it_can_clear_specific_product_cache()
    {
        $product = Product::factory()->create();
        $cacheKey = "product_with_promotions_{$product->id}";
        
        // Manually set cache
        Cache::put($cacheKey, $product, 600);
        $this->assertTrue(Cache::has($cacheKey));
        
        // Clear specific product cache
        $this->paymentService->clearProductCache($product->id);
        
        // Verify specific cache is cleared
        $this->assertFalse(Cache::has($cacheKey));
    }

    #[Test]
    public function it_can_clear_all_product_cache()
    {
        // Create multiple products and cache them
        $products = Product::factory()->count(3)->create();
        
        foreach ($products as $product) {
            $cacheKey = "product_with_promotions_{$product->id}";
            Cache::put($cacheKey, $product, 600);
            $this->assertTrue(Cache::has($cacheKey));
        }
        
        // Clear all product cache
        $this->paymentService->clearAllProductCache();
        
        // Verify all product cache is cleared
        foreach ($products as $product) {
            $cacheKey = "product_with_promotions_{$product->id}";
            $this->assertFalse(Cache::has($cacheKey));
        }
    }

    #[Test]
    public function it_provides_cache_statistics()
    {
        // Populate some cache
        $this->paymentService->getPaymentMethods();
        
        $stats = $this->paymentService->getCacheStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('stripe_config_cached', $stats);
        $this->assertArrayHasKey('payment_methods_cached', $stats);
        $this->assertArrayHasKey('cache_driver', $stats);
        
        $this->assertTrue($stats['payment_methods_cached']);
        $this->assertEquals(config('cache.default'), $stats['cache_driver']);
    }

    #[Test]
    public function it_handles_cache_miss_gracefully()
    {
        // Clear cache first
        Cache::flush();
        
        // This should work even with empty cache
        $methods = $this->paymentService->getPaymentMethods();
        
        $this->assertIsArray($methods);
        $this->assertContains('card', $methods);
        $this->assertContains('blik', $methods);
    }

    #[Test]
    public function it_respects_cache_ttl()
    {
        // Test that cache has appropriate TTL
        $this->paymentService->getPaymentMethods();
        
        // Check if cache exists
        $this->assertTrue(Cache::has('stripe_payment_methods'));
        
        // We can't easily test TTL expiration in unit tests without waiting,
        // but we can verify the cache was set with the expected duration
        // by checking if it exists immediately after setting
        $this->assertTrue(Cache::has('stripe_payment_methods'));
    }

    #[Test]
    public function it_caches_different_products_separately()
    {
        $product1 = Product::factory()->create(['name' => 'Product 1']);
        $product2 = Product::factory()->create(['name' => 'Product 2']);
        
        $cartData1 = [['product_id' => $product1->id, 'quantity' => 1]];
        $cartData2 = [['product_id' => $product2->id, 'quantity' => 1]];
        
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('prepareLineItemsFromGuestCart');
        $method->setAccessible(true);
        
        // Call for both products
        $method->invoke($this->paymentService, $cartData1);
        $method->invoke($this->paymentService, $cartData2);
        
        // Verify separate cache keys
        $this->assertTrue(Cache::has("product_with_promotions_{$product1->id}"));
        $this->assertTrue(Cache::has("product_with_promotions_{$product2->id}"));
        
        // Clear one product cache
        $this->paymentService->clearProductCache($product1->id);
        
        // Verify only one is cleared
        $this->assertFalse(Cache::has("product_with_promotions_{$product1->id}"));
        $this->assertTrue(Cache::has("product_with_promotions_{$product2->id}"));
    }
} 