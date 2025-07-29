<?php

namespace Tests\Unit\Services\Payment;

use Tests\TestCase;
use App\Services\Payment\PaymentService;
use App\Services\ShippingService;
use App\Models\User;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentService;
    protected $shippingServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock ShippingService
        $this->shippingServiceMock = Mockery::mock(ShippingService::class);
        $this->paymentService = new PaymentService($this->shippingServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_get_default_payment_methods()
    {
        $methods = $this->paymentService->getPaymentMethods();

        $this->assertContains('card', $methods);
        $this->assertContains('blik', $methods);
        $this->assertContains('p24', $methods); // Default enabled
    }

    #[Test]
    public function it_can_get_payment_methods_without_p24_when_disabled()
    {
        config(['services.stripe.p24.enabled' => false]);
        
        $methods = $this->paymentService->getPaymentMethods();

        $this->assertContains('card', $methods);
        $this->assertContains('blik', $methods);
        $this->assertNotContains('p24', $methods);
    }

    #[Test]
    public function it_throws_exception_when_stripe_key_not_configured()
    {
        config(['services.stripe.secret' => '']);
        \Illuminate\Support\Facades\Cache::forget('stripe_secret_key');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stripe secret key is not configured. Please check your .env file.');

        new PaymentService($this->shippingServiceMock);
    }

    #[Test]
    public function it_throws_exception_when_user_cart_is_empty_for_checkout_session()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Koszyk jest pusty');

        $this->paymentService->createCheckoutSession(
            ['email' => 'test@example.com'],
            'courier'
        );
    }

    #[Test]
    public function it_throws_exception_when_user_cart_is_empty_for_payment_intent()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Koszyk jest pusty');

        $this->paymentService->createPaymentIntent();
    }

    #[Test]
    public function it_throws_exception_for_guest_cart_with_invalid_total()
    {
        $cartData = [
            ['product_id' => 999, 'quantity' => 1] // Non-existent product
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nieprawidłowa suma zamówienia');

        $this->paymentService->createGuestPaymentIntent($cartData);
    }

    #[Test]
    public function it_throws_exception_for_guest_checkout_with_no_products()
    {
        $cartData = [
            ['product_id' => 999, 'quantity' => 1] // Non-existent product
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Brak produktów w koszyku');

        $this->paymentService->createGuestCheckoutSession(
            $cartData,
            ['email' => 'test@example.com'],
            'courier'
        );
    }

    #[Test]
    public function it_can_check_payment_status()
    {
        // Mock Stripe PaymentIntent
        $paymentIntentMock = Mockery::mock();
        $paymentIntentMock->id = 'pi_test_123';
        $paymentIntentMock->status = 'succeeded';
        $paymentIntentMock->amount = 1000;
        $paymentIntentMock->currency = 'pln';
        $paymentIntentMock->payment_method_types = ['card'];
        $paymentIntentMock->last_payment_error = null;
        $paymentIntentMock->created = 1234567890;
        $paymentIntentMock->updated = 1234567890;

        // Mock Stripe SDK
        $stripeMock = Mockery::mock('alias:Stripe\PaymentIntent');
        $stripeMock->shouldReceive('retrieve')
            ->with('pi_test_123')
            ->andReturn($paymentIntentMock);

        $result = $this->paymentService->checkPaymentStatus('pi_test_123');

        $this->assertEquals('pi_test_123', $result['payment_intent_id']);
        $this->assertEquals('succeeded', $result['status']);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('pln', $result['currency']);
        $this->assertEquals('card', $result['payment_method']);
    }

    #[Test]
    public function it_can_calculate_cart_total_with_promotional_prices()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        
        // Mock getPromotionalPrice method
        $product = Mockery::mock($product);
        $product->shouldReceive('getPromotionalPrice')->andReturn(80); // 20% discount

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $cartItem->product = $product;

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('calculateCartTotal');
        $method->setAccessible(true);

        $total = $method->invoke($this->paymentService, collect([$cartItem]));

        $this->assertEquals(160, $total); // 2 * 80
    }

    #[Test]
    public function it_can_calculate_guest_cart_total()
    {
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 50]);

        $cartData = [
            ['product_id' => $product1->id, 'quantity' => 2],
            ['product_id' => $product2->id, 'quantity' => 1]
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('calculateGuestCartTotal');
        $method->setAccessible(true);

        $total = $method->invoke($this->paymentService, $cartData);

        // Calculate expected total based on actual product prices
        $expectedTotal = (2 * $product1->price) + (1 * $product2->price);
        $this->assertEquals($expectedTotal, $total);
    }

    #[Test]
    public function it_can_prepare_line_items_from_cart()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 100
        ]);

        // Mock getPromotionalPrice method
        $product = Mockery::mock($product);
        $product->name = 'Test Product';
        $product->description = 'Test Description';
        $product->shouldReceive('getPromotionalPrice')->andReturn(80);

        $cartItem = Mockery::mock();
        $cartItem->product = $product;
        $cartItem->quantity = 2;

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->paymentService);
        $method = $reflection->getMethod('prepareLineItemsFromCart');
        $method->setAccessible(true);

        $lineItems = $method->invoke($this->paymentService, collect([$cartItem]));

        $this->assertCount(1, $lineItems);
        $this->assertEquals('Test Product', $lineItems[0]['price_data']['product_data']['name']);
        $this->assertEquals('Test Description', $lineItems[0]['price_data']['product_data']['description']);
        $this->assertEquals(8000, $lineItems[0]['price_data']['unit_amount']); // 80 * 100 grosze
        $this->assertEquals(2, $lineItems[0]['quantity']);
        $this->assertEquals('pln', $lineItems[0]['price_data']['currency']);
    }
} 