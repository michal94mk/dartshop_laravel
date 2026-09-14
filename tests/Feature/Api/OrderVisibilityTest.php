<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderVisibilityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function order_details_are_not_publicly_accessible()
    {
        $order = Order::factory()->create(['email' => 'victim@example.com']);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(404);
        $response->assertDontSee('victim@example.com');
    }

    #[Test]
    public function guests_cannot_read_orders_through_the_owner_scoped_endpoint()
    {
        $order = Order::factory()->create();

        $this->getJson("/api/orders/my-orders/{$order->id}")
            ->assertStatus(401);
    }

    #[Test]
    public function a_user_cannot_read_another_users_order()
    {
        $order = Order::factory()->create([
            'user_id' => User::factory(),
            'email' => 'owner@example.com',
        ]);

        /** @var User $intruder */
        $intruder = User::factory()->createOne();

        $response = $this->actingAs($intruder)
            ->getJson("/api/orders/my-orders/{$order->id}");

        $response->assertStatus(404);
        $response->assertDontSee('owner@example.com');
    }

    #[Test]
    public function a_user_can_read_their_own_order()
    {
        /** @var User $owner */
        $owner = User::factory()->createOne();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)
            ->getJson("/api/orders/my-orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $order->id);
    }
}
