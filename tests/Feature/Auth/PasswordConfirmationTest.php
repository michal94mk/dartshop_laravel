<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_update_requires_authentication(): void
    {
        // Test that password update API requires authentication
        $response = $this->putJson('/api/user/password', [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    public function test_profile_update_requires_authentication(): void
    {
        // Test that profile update API requires authentication
        $response = $this->putJson('/api/user/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(401); // Unauthorized
    }
}
