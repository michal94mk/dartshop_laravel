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
        // Dostarcz dane testowe, w tym wymagane pole 'name'
        $userData = [
            'name' => 'user1',
            'email' => 'user1@example.com',
            'password' => '123abc',
            'role' => 'user',
        ];

        // Utwórz użytkownika na podstawie danych testowych
        $user = User::create($userData);

        // Sprawdź, czy użytkownik został poprawnie utworzony
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['role'], $user->role);

        // Możesz również sprawdzić inne aspekty, takie jak czy użytkownik ma domyślną rolę itp.
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
