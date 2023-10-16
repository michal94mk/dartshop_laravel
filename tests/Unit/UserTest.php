<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCreationWithRequiredFields()
    {
        $userData = [
            'name' => 'user1',
            'email' => 'user1@example.com',
            'password' => '123abc',
            'role' => 'user',
        ];

        $user = User::create($userData);

        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['role'], $user->role);
    }

    public function testEmailIsRequired()
    {
        $response = $this->post('/register', [
            'name' => 'username',
            'password' => '123abc',
            'role' => 'user',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
