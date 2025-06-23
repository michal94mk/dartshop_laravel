<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Http\Middleware\RoleMiddleware;


class RoleMiddlewareTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function testAuthorizedUserWithCorrectRoleCanAccessRoute()
    {
        // Create admin role
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        
        $user = User::factory()->create(['is_admin' => true]);
        $user->assignRole('admin');
        Auth::login($user);
        $request = Request::create('/protected-route');
        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, function () {
            return new Response('Authorized', 200);
        }, 'admin');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Authorized', $response->getContent());
    }

    public function testUnauthorizedUserIsBlockedFromRoute()
    {
        // Create user role
        \Spatie\Permission\Models\Role::create(['name' => 'user']);
        
        $user = User::factory()->create(['is_admin' => false]);
        $user->assignRole('user');
        $this->actingAs($user);
        $request = Request::create('/protected-route');
        $this->expectException(AuthorizationException::class);
        $middleware = new RoleMiddleware();
        $middleware->handle($request, function () {}, 'admin');
    }


    public function testUnauthenticatedUserIsBlockedFromRoute()
    {
        Auth::logout();
        $request = Request::create('/protected-route');
        $this->expectException(AuthenticationException::class);
        $middleware = new RoleMiddleware();
        $middleware->handle($request, function () {});
    }
}
