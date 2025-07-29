<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles first
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $userRole = \Spatie\Permission\Models\Role::create(['name' => 'user']);

        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true
        ]);
        $this->admin->assignRole('admin');

        // Create regular user
        $this->user = User::factory()->create([
            'email' => 'user@example.com'
        ]);
        $this->user->assignRole('user');

        // Create product
        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100.00
        ]);
    }

    #[Test]
    public function it_prevents_creating_more_than_6_featured_reviews()
    {
        // Create 6 approved featured reviews
        for ($i = 0; $i < 6; $i++) {
            $product = Product::factory()->create();
            Review::factory()->create([
                'user_id' => $this->user->id,
                'product_id' => $product->id,
                'is_featured' => true,
                'is_approved' => true
            ]);
        }

        // Try to create a 7th featured review
        $product = Product::factory()->create();
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/reviews', [
                'user_id' => $this->user->id,
                'product_id' => $product->id,
                'rating' => 5,
                'title' => 'Test Review',
                'content' => 'Test content',
                'is_approved' => true,
                'is_featured' => true
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Możesz wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji, aby dodać nową.'
        ]);

        // Verify only 6 featured reviews exist
        $this->assertEquals(6, Review::where('is_featured', true)->count());
    }

    #[Test]
    public function it_prevents_toggling_to_featured_when_limit_reached()
    {
        // Create 6 approved featured reviews
        for ($i = 0; $i < 6; $i++) {
            $product = Product::factory()->create();
            Review::factory()->create([
                'user_id' => $this->user->id,
                'product_id' => $product->id,
                'is_featured' => true,
                'is_approved' => true
            ]);
        }

        // Create a non-featured review
        $product = Product::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_featured' => false,
            'is_approved' => true
        ]);

        // Try to toggle it to featured
        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/reviews/{$review->id}/toggle-featured");

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Możesz wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji, aby dodać nową.'
        ]);

        // Verify the review is still not featured
        $this->assertEquals(0, $review->fresh()->is_featured);
    }

    #[Test]
    public function it_allows_toggling_featured_when_under_limit()
    {
        // Create 5 approved featured reviews
        for ($i = 0; $i < 5; $i++) {
            $product = Product::factory()->create();
            Review::factory()->create([
                'user_id' => $this->user->id,
                'product_id' => $product->id,
                'is_featured' => true,
                'is_approved' => true
            ]);
        }

        // Create a non-featured review
        $product = Product::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_featured' => false,
            'is_approved' => true
        ]);

        // Try to toggle it to featured
        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/reviews/{$review->id}/toggle-featured");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Recenzja została wyróżniona.'
        ]);

        // Verify the review is now featured from API response
        $this->assertTrue($response->json('data.review.is_featured'));
    }

    #[Test]
    public function it_allows_removing_featured_status()
    {
        // Create a featured review
        $product = Product::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_featured' => true,
            'is_approved' => true
        ]);

        // Try to remove featured status
        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/reviews/{$review->id}/toggle-featured");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Recenzja została usunięta z wyróżnienia.'
        ]);

        // Verify the review is no longer featured from API response
        $this->assertFalse($response->json('data.review.is_featured'));
    }

    #[Test]
    public function it_prevents_updating_to_featured_when_limit_reached()
    {
        // Create 6 approved featured reviews
        for ($i = 0; $i < 6; $i++) {
            $product = Product::factory()->create();
            Review::factory()->create([
                'user_id' => $this->user->id,
                'product_id' => $product->id,
                'is_featured' => true,
                'is_approved' => true
            ]);
        }

        // Create a non-featured review
        $product = Product::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'is_featured' => false,
            'is_approved' => true
        ]);

        // Try to update it to featured
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/reviews/{$review->id}", [
                'user_id' => $this->user->id,
                'product_id' => $product->id,
                'rating' => 5,
                'title' => 'Updated Review',
                'content' => 'Updated content',
                'is_approved' => true,
                'is_featured' => true
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Możesz wyróżnić maksymalnie 6 recenzji. Usuń wyróżnienie z innej recenzji, aby dodać nową.'
        ]);

        // Verify the review is still not featured
        $this->assertEquals(0, $review->fresh()->is_featured);
    }

    #[Test]
    public function it_prevents_featuring_unapproved_review()
    {
        // Create an unapproved review
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'is_featured' => false,
            'is_approved' => false
        ]);

        // Try to toggle it to featured
        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/reviews/{$review->id}/toggle-featured");

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Recenzja musi być zatwierdzona, aby ją wyróżnić.'
        ]);

        // Verify the review is still not featured
        $this->assertEquals(0, $review->fresh()->is_featured);
    }

    #[Test]
    public function it_prevents_creating_featured_unapproved_review()
    {
        // Try to create a featured but unapproved review
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/reviews', [
                'user_id' => $this->user->id,
                'product_id' => $this->product->id,
                'rating' => 5,
                'title' => 'Test Review',
                'content' => 'Test content',
                'is_approved' => false,
                'is_featured' => true
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Recenzja musi być zatwierdzona, aby ją wyróżnić.'
        ]);
    }
} 