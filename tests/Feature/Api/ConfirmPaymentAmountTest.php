<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\User;
use App\Models\CartItem;
use App\Services\OrderService;
use App\Services\Payment\PaymentService;
use App\Exceptions\PaymentAmountMismatchException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConfirmPaymentAmountTest extends TestCase
{
    use RefreshDatabase;

    private function shippingPayload(string $method = 'courier'): array
    {
        return [
            'payment_intent_id' => 'pi_test_amount',
            'shipping_method' => $method,
            'shipping' => [
                'name' => 'Jan Kowalski',
                'email' => 'jan@example.com',
                'address' => 'ul. Testowa 1',
                'city' => 'Warszawa',
                'postalCode' => '00-001',
            ],
        ];
    }

    private function stubStripeRetrieve(int $amountCents, string $id = 'pi_test_amount'): void
    {
        $paymentIntent = (object) [
            'id' => $id,
            'status' => 'succeeded',
            'amount' => $amountCents,
            'currency' => 'pln',
        ];

        // PaymentService::getPaymentIntent calls PaymentIntent::retrieve
        $this->mock(PaymentService::class, function ($mock) use ($paymentIntent, $id) {
            $real = new PaymentService(
                app(\App\Services\ShippingService::class),
                app(OrderService::class)
            );

            $mock->shouldReceive('getPaymentIntent')
                ->with($id)
                ->andReturn($paymentIntent);

            $mock->shouldReceive('pullCartSnapshot')
                ->andReturnUsing(fn ($piId) => $real->pullCartSnapshot($piId));
            $mock->shouldReceive('forgetCartSnapshot')
                ->andReturnUsing(fn ($piId) => $real->forgetCartSnapshot($piId));
            $mock->shouldReceive('assertPaymentIntentMatchesQuote')
                ->andReturnUsing(fn ($pi, $expected) => $real->assertPaymentIntentMatchesQuote($pi, $expected));
        });
    }

    private function storeGuestSnapshot(Product $product, string $method = 'pickup'): array
    {
        $quote = app(OrderService::class)->quoteFromGuestCart([
            ['product_id' => $product->id, 'quantity' => 1],
        ], $method);

        Cache::put('payment_intent_cart:pi_test_amount', [
            'user_id' => null,
            'guest' => true,
            'shipping_method' => $method,
            'lines' => $quote['lines'],
            'expected_amount_cents' => $quote['total_cents'],
        ], 3600);

        return $quote;
    }

    #[Test]
    public function guest_confirm_rejects_when_paid_amount_does_not_match_frozen_quote()
    {
        $cheap = Product::factory()->create(['price' => 10.00]);
        $this->storeGuestSnapshot($cheap, 'pickup');
        $this->stubStripeRetrieve(1);

        $response = $this->postJson('/api/guest-stripe/confirm-payment', $this->shippingPayload('pickup'));

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Kwota opłacona w Stripe nie zgadza się z wartością zamówienia.',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    #[Test]
    public function guest_confirm_creates_order_from_snapshot_ignoring_client_cart_items()
    {
        $cheap = Product::factory()->create(['price' => 10.00, 'name' => 'Cheap Dart']);
        $expensive = Product::factory()->create(['price' => 500.00, 'name' => 'Expensive Dart']);
        $quote = $this->storeGuestSnapshot($cheap, 'pickup');

        $this->stubStripeRetrieve($quote['total_cents']);

        $payload = $this->shippingPayload('pickup');
        $payload['cart_items'] = [
            ['product_id' => $expensive->id, 'quantity' => 1],
        ];

        $response = $this->postJson('/api/guest-stripe/confirm-payment', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'email' => 'jan@example.com',
            'total' => $quote['total'],
            'payment_intent_id' => 'pi_test_amount',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $cheap->id,
            'product_name' => 'Cheap Dart',
        ]);
        $this->assertDatabaseMissing('order_items', [
            'product_id' => $expensive->id,
        ]);
    }

    #[Test]
    public function guest_confirm_rejects_shipping_method_tampering()
    {
        $cheap = Product::factory()->create(['price' => 10.00]);
        $quote = $this->storeGuestSnapshot($cheap, 'pickup');
        $this->stubStripeRetrieve($quote['total_cents']);

        $response = $this->postJson(
            '/api/guest-stripe/confirm-payment',
            $this->shippingPayload('express')
        );

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
    }

    #[Test]
    public function authenticated_confirm_rejects_someone_elses_payment_intent()
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $quote = app(OrderService::class)->quoteFromUserCart($owner, 'pickup');

        Cache::put('payment_intent_cart:pi_test_amount', [
            'user_id' => $owner->id,
            'guest' => false,
            'shipping_method' => 'pickup',
            'lines' => $quote['lines'],
            'expected_amount_cents' => $quote['total_cents'],
        ], 3600);

        $this->stubStripeRetrieve($quote['total_cents']);

        $response = $this->actingAs($intruder)
            ->postJson('/api/stripe/confirm-payment', $this->shippingPayload('pickup'));

        $response->assertStatus(403);
        $this->assertDatabaseCount('orders', 0);
    }

    #[Test]
    public function assert_payment_intent_matches_quote_throws_on_mismatch()
    {
        $service = app(PaymentService::class);
        $pi = (object) ['amount' => 100, 'currency' => 'pln'];

        $this->expectException(PaymentAmountMismatchException::class);
        $service->assertPaymentIntentMatchesQuote($pi, 999);
    }
}
