<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            throw new AuthenticationException('Unauthenticated.');
        }

        // Check if user has any of the requested roles
        $hasRole = false;
        foreach ($roles as $role) {
            if (Auth::user()->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            throw new AuthorizationException('Unauthorized. You do not have access to this page.');
        }

        view()->share('is_admin', Auth::user()->hasRole('admin'));

        return $next($request);
    }
}
