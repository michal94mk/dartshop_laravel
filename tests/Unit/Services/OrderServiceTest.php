<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OrderService;
use App\Services\ShippingService;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;
    protected $shippingServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shippingServiceMock = Mockery::mock(ShippingService::class);
        $this->orderService = new OrderService($this->shippingServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_create_order_from_user_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        
        // Mock promotional price
        $product = Mockery::mock($product);
        $product->shouldReceive('getPromotionalPrice')->andReturn(80);
        $product->id = 1;
        $product->name = 'Test Product';

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        $cartItem->product = $product;

        // Mock shipping service
        $this->shippingServiceMock
            ->shouldReceive('calculateShippingCost')
            ->with('courier', 160.0)
            ->andReturn(15.0);

        $shippingData = [
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'address' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postalCode' => '00-001'
        ];

        // Mock Order generation method
        Order::shouldReceive('generateOrderNumber')->andReturn('ORD-123456');

        $order = $this->orderService->createOrderFromCart(
            $user,
            $shippingData,
            'courier',
            'pi_test_123'
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals('Jan', $order->first_name);
        $this->assertEquals('Kowalski', $order->last_name);
        $this->assertEquals('jan@example.com', $order->email);
        $this->assertEquals(160.0, $order->subtotal);
        $this->assertEquals(15.0, $order->shipping_cost);
        $this->assertEquals(175.0, $order->total);
        $this->assertEquals('pi_test_123', $order->payment_intent_id);
        $this->assertEquals(OrderStatus::Processing, $order->status);
    }

    /** @test */
    public function it_throws_exception_when_user_cart_is_empty()
    {
        $user = User::factory()->create();
        
        $shippingData = [
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'address' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postalCode' => '00-001'
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Koszyk jest pusty');

        $this->orderService->createOrderFromCart(
            $user,
            $shippingData,
            'courier',
            'pi_test_123'
        );
    }

    /** @test */
    public function it_can_create_order_from_guest_cart()
    {
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 50]);

        // Mock promotional prices
        $product1 = Mockery::mock($product1);
        $product1->shouldReceive('getPromotionalPrice')->andReturn(80);
        $product1->id = 1;
        $product1->name = 'Product 1';

        $product2 = Mockery::mock($product2);
        $product2->shouldReceive('getPromotionalPrice')->andReturn(40);
        $product2->id = 2;
        $product2->name = 'Product 2';

        // Mock Product::with()->find() calls
        Product::shouldReceive('with')
            ->with('activePromotions')
            ->andReturnSelf();
        
        Product::shouldReceive('find')
            ->with(1)
            ->andReturn($product1);
            
        Product::shouldReceive('find')
            ->with(2)
            ->andReturn($product2);

        $cartData = [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 2, 'quantity' => 1]
        ];

        // Mock shipping service
        $this->shippingServiceMock
            ->shouldReceive('calculateShippingCost')
            ->with('courier', 200.0)
            ->andReturn(15.0);

        $shippingData = [
            'name' => 'Anna Nowak',
            'email' => 'anna@example.com',
            'address' => 'ul. Gościniecka 2',
            'city' => 'Kraków',
            'postalCode' => '30-001'
        ];

        // Mock Order generation method
        Order::shouldReceive('generateOrderNumber')->andReturn('ORD-654321');

        $order = $this->orderService->createOrderFromGuestCart(
            $cartData,
            $shippingData,
            'courier',
            'pi_test_456'
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertNull($order->user_id);
        $this->assertEquals('Anna', $order->first_name);
        $this->assertEquals('Nowak', $order->last_name);
        $this->assertEquals('anna@example.com', $order->email);
        $this->assertEquals(200.0, $order->subtotal);
        $this->assertEquals(15.0, $order->shipping_cost);
        $this->assertEquals(215.0, $order->total);
        $this->assertEquals('pi_test_456', $order->payment_intent_id);
    }

    /** @test */
    public function it_throws_exception_for_guest_cart_with_non_existent_product()
    {
        Product::shouldReceive('with')
            ->with('activePromotions')
            ->andReturnSelf();
            
        Product::shouldReceive('find')
            ->with(999)
            ->andReturn(null);

        $cartData = [
            ['product_id' => 999, 'quantity' => 1]
        ];

        $shippingData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'city' => 'Test City',
            'postalCode' => '00-000'
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Produkt o ID 999 nie istnieje');

        $this->orderService->createOrderFromGuestCart(
            $cartData,
            $shippingData,
            'courier',
            'pi_test_789'
        );
    }

    /** @test */
    public function it_can_validate_shipping_method()
    {
        $this->shippingServiceMock
            ->shouldReceive('isValidMethod')
            ->with('courier')
            ->andReturn(true);

        $this->shippingServiceMock
            ->shouldReceive('isValidMethod')
            ->with('invalid_method')
            ->andReturn(false);

        $this->assertTrue($this->orderService->validateShippingMethod('courier'));
        $this->assertFalse($this->orderService->validateShippingMethod('invalid_method'));
    }

    /** @test */
    public function it_can_check_if_order_exists_by_payment_intent()
    {
        $order = Order::factory()->create(['payment_intent_id' => 'pi_existing_123']);

        $existingOrder = $this->orderService->orderExistsByPaymentIntent('pi_existing_123');
        $nonExistingOrder = $this->orderService->orderExistsByPaymentIntent('pi_non_existing_456');

        $this->assertInstanceOf(Order::class, $existingOrder);
        $this->assertEquals('pi_existing_123', $existingOrder->payment_intent_id);
        $this->assertNull($nonExistingOrder);
    }

    /** @test */
    public function it_can_check_if_order_exists_by_stripe_session()
    {
        $order = Order::factory()->create(['stripe_session_id' => 'cs_existing_123']);

        $existingOrder = $this->orderService->orderExistsByStripeSession('cs_existing_123');
        $nonExistingOrder = $this->orderService->orderExistsByStripeSession('cs_non_existing_456');

        $this->assertInstanceOf(Order::class, $existingOrder);
        $this->assertEquals('cs_existing_123', $existingOrder->stripe_session_id);
        $this->assertNull($nonExistingOrder);
    }

    /** @test */
    public function it_correctly_parses_full_name()
    {
        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->orderService);
        $method = $reflection->getMethod('parseName');
        $method->setAccessible(true);

        // Test single name
        $result = $method->invoke($this->orderService, 'Jan');
        $this->assertEquals('Jan', $result['first_name']);
        $this->assertEquals('', $result['last_name']);

        // Test two names
        $result = $method->invoke($this->orderService, 'Jan Kowalski');
        $this->assertEquals('Jan', $result['first_name']);
        $this->assertEquals('Kowalski', $result['last_name']);

        // Test multiple names (should take only first and rest)
        $result = $method->invoke($this->orderService, 'Jan Maria Kowalski');
        $this->assertEquals('Jan', $result['first_name']);
        $this->assertEquals('Maria Kowalski', $result['last_name']);

        // Test with extra spaces
        $result = $method->invoke($this->orderService, '  Jan   Kowalski  ');
        $this->assertEquals('Jan', $result['first_name']);
        $this->assertEquals('Kowalski', $result['last_name']);
    }

    /** @test */
    public function it_calculates_cart_subtotal_correctly()
    {
        $product1 = Mockery::mock();
        $product1->shouldReceive('getPromotionalPrice')->andReturn(80);

        $product2 = Mockery::mock();
        $product2->shouldReceive('getPromotionalPrice')->andReturn(40);

        $cartItem1 = Mockery::mock();
        $cartItem1->product = $product1;
        $cartItem1->quantity = 2;

        $cartItem2 = Mockery::mock();
        $cartItem2->product = $product2;
        $cartItem2->quantity = 3;

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->orderService);
        $method = $reflection->getMethod('calculateCartSubtotal');
        $method->setAccessible(true);

        $subtotal = $method->invoke($this->orderService, collect([$cartItem1, $cartItem2]));

        $this->assertEquals(280.0, $subtotal); // (80*2) + (40*3) = 160 + 120 = 280
    }

    /** @test */
    public function it_calculates_guest_cart_subtotal_correctly()
    {
        $cartItems = [
            ['price' => 80, 'quantity' => 2],
            ['price' => 40, 'quantity' => 3]
        ];

        // Use reflection to test private method
        $reflection = new \ReflectionClass($this->orderService);
        $method = $reflection->getMethod('calculateGuestCartSubtotal');
        $method->setAccessible(true);

        $subtotal = $method->invoke($this->orderService, $cartItems);

        $this->assertEquals(280.0, $subtotal); // (80*2) + (40*3) = 160 + 120 = 280
    }

    /** @test */
    public function it_runs_in_database_transaction()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        
        // Mock promotional price
        $product = Mockery::mock($product);
        $product->shouldReceive('getPromotionalPrice')->andReturn(80);
        $product->id = 1;
        $product->name = 'Test Product';

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        $cartItem->product = $product;

        // Mock shipping service to throw exception
        $this->shippingServiceMock
            ->shouldReceive('calculateShippingCost')
            ->andThrow(new \Exception('Shipping calculation failed'));

        $shippingData = [
            'name' => 'Jan Kowalski',
            'email' => 'jan@example.com',
            'address' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postalCode' => '00-001'
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Shipping calculation failed');

        // Verify no order was created due to transaction rollback
        $orderCountBefore = Order::count();
        
        try {
            $this->orderService->createOrderFromCart(
                $user,
                $shippingData,
                'courier',
                'pi_test_123'
            );
        } catch (\Exception $e) {
            $orderCountAfter = Order::count();
            $this->assertEquals($orderCountBefore, $orderCountAfter);
            throw $e;
        }
    }
} 