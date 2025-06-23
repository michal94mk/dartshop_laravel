<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonStructure(['id', 'name', 'email']);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this
            ->actingAs($user)
            ->putJson('/api/user/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Profil został zaktualizowany. Ponieważ zmieniłeś adres e-mail, musisz go ponownie zweryfikować. Link weryfikacyjny został wysłany.'
        ]);

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        // Po zmianie emaila, email_verified_at powinien być null
        $this->assertNull($user->email_verified_at, 'Email verification should be reset when email changes');
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this
            ->actingAs($user)
            ->putJson('/api/user/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Profil został pomyślnie zaktualizowany.'
        ]);

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->putJson('/api/user/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Hasło zostało pomyślnie zmienione.'
        ]);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password', $user->fresh()->password));
    }

    public function test_correct_current_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->putJson('/api/user/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['current_password']);
    }
}
