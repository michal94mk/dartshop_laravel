<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required roles
        \Spatie\Permission\Models\Role::create(['name' => 'user']);
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'privacy_policy_accepted' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }
}
