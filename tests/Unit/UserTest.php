<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles needed for tests
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function testUserCreationWithRequiredFields()
    {
        $userData = [
            'name' => 'user1',
            'email' => 'user1@example.com',
            'password' => '123abc',
        ];

        $user = User::create($userData);
        $user->assignRole('user');

        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertTrue($user->hasRole('user'));
    }

    public function testEmailIsRequired()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'username',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => '123abc123',
            'password_confirmation' => '123abc123',
            'privacy_policy_accepted' => true,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}
