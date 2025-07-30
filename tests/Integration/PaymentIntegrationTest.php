<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;

class PaymentIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00
        ]);
    }

    #[Test]
    public function test_payment_creation()
    {
        // Create order
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 100.00
        ]);

        // Create payment
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'payment_status' => 'pending'
        ]);

        // Verify payment was created
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'payment_status' => 'pending'
        ]);
    }

    #[Test]
    public function test_payment_status_updates()
    {
        // Create order and payment
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 100.00
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'payment_status' => 'pending'
        ]);

        // Update payment status
        $payment->update(['payment_status' => 'completed']);

        // Verify payment status was updated
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_status' => 'completed'
        ]);
    }

    #[Test]
    public function test_payment_method_handling()
    {
        // Create order and payment with different payment methods
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 100.00
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'payment_method' => 'stripe',
            'payment_status' => 'completed'
        ]);

        // Verify payment method was set
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_method' => 'stripe'
        ]);
    }

    #[Test]
    public function test_payment_metadata_storage()
    {
        // Create order and payment with metadata
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 100.00
        ]);

        $paymentData = [
            'customer_email' => 'test@example.com',
            'customer_name' => 'Test User'
        ];

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'payment_data' => json_encode($paymentData),
            'payment_status' => 'completed'
        ]);

        // Verify payment data was stored
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
        ]);
        
        // Verify payment data exists (checking content separately due to JSON encoding)
        $paymentFromDb = \App\Models\Payment::find($payment->id);
        $this->assertNotNull($paymentFromDb);
        $this->assertEquals($paymentData, json_decode($paymentFromDb->payment_data, true));
    }

    #[Test]
    public function test_payment_currency_handling()
    {
        // Create order and payment with PLN currency
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 100.00
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'currency' => 'PLN',
            'payment_status' => 'completed'
        ]);

        // Verify currency was set
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'currency' => 'PLN'
        ]);
    }

    #[Test]
    public function test_payment_failed_handling()
    {
        // Create order and failed payment
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => OrderStatus::Processing,
            'total' => 100.00
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'user_id' => $this->user->id,
            'amount' => 100.00,
            'payment_status' => 'failed'
        ]);

        // Verify failed payment was created
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_status' => 'failed'
        ]);
    }
} 