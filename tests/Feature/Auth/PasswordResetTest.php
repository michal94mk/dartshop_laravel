<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\QueuedResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        // Test API endpoint validation instead of web route for SPA
        $response = $this->postJson('/api/forgot-password', ['email' => '']);

        // Should validate required email field
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);
        $response->assertOk();
        $response->assertJson(['message' => 'We have emailed your password reset link!']);
        Notification::assertSentTo($user, QueuedResetPasswordNotification::class);
    }

    public function test_reset_password_validation_works(): void
    {
        // Test API validation for reset password
        $response = $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => '123', // Too short
            'password_confirmation' => '456', // Different
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);
        $response->assertOk();

        Notification::assertSentTo($user, QueuedResetPasswordNotification::class);

        $notification = Notification::sent($user, QueuedResetPasswordNotification::class)->first();
        
        $response = $this->postJson('/api/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['message']);
    }
}
