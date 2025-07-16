<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Promotion;

class PromotionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $promotion;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true
        ]);
        
        // Create regular user
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'is_admin' => false
        ]);
        
        // Create promotion
        $this->promotion = Promotion::factory()->create([
            'title' => 'Test Promotion',
            'name' => 'TEST10',
            'discount_type' => 'percentage',
            'discount_value' => 10.00
        ]);
    }

    /** @test */
    public function admin_can_view_promotions_list()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/promotions');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'name',
                    'discount_type',
                    'discount_value',
                    'is_active'
                ]
            ],
            'current_page',
            'per_page',
            'total'
        ]);
    }

    /** @test */
    public function admin_can_create_promotion()
    {
        $promotionData = [
            'title' => 'New Promotion',
            'name' => 'NEW20',
            'description' => 'New promotion description',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'code' => 'NEW20',
            'starts_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/promotions', $promotionData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('promotions', ['name' => 'NEW20']);
    }

    /** @test */
    public function admin_can_update_promotion()
    {
        $updateData = [
            'title' => 'Updated Promotion',
            'name' => 'UPDATED25',
            'discount_type' => 'percentage',
            'discount_value' => 25.00,
            'starts_at' => now()->addDays(1)->format('Y-m-d H:i:s')
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/promotions/{$this->promotion->id}", $updateData);

        $response->assertStatus(200);
        $this->assertEquals('Updated Promotion', $this->promotion->fresh()->title);
    }

    /** @test */
    public function admin_can_delete_promotion()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/promotions/{$this->promotion->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('promotions', ['id' => $this->promotion->id]);
    }

    /** @test */
    public function non_admin_cannot_access_promotions()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/promotions');

        $response->assertStatus(403);
    }
} 