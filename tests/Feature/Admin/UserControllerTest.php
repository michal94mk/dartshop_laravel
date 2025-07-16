<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;

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
    }

    /** @test */
    public function admin_can_view_users_list()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'is_admin'
                ]
            ]
        ]);
    }

    /** @test */
    public function admin_can_view_single_user()
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/users/{$this->user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $this->user->id,
            'email' => $this->user->email
        ]);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $updateData = [
            'name' => 'Updated Name',
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
            'role' => 'user'
        ];

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/users/{$this->user->id}", $updateData);

        $response->assertStatus(200);
        $this->assertEquals('Updated Name', $this->user->fresh()->name);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/users/{$this->user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    /** @test */
    public function non_admin_cannot_access_users()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/users');

        $response->assertStatus(403);
    }
} 