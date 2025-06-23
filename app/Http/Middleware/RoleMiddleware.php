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

        $user = Auth::user();
        
        // Check if user has any of the requested roles
        $hasRole = false;
        
        foreach ($roles as $role) {
            // Check admin role specifically with is_admin field for backward compatibility
            if ($role === 'admin' && $user->is_admin) {
                $hasRole = true;
                break;
            }
            
            // Also check using Spatie roles if method exists
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            throw new AuthorizationException('Unauthorized. You do not have access to this page.');
        }

        // Share admin status with views
        view()->share('is_admin', $user->is_admin || (method_exists($user, 'hasRole') && $user->hasRole('admin')));

        return $next($request);
    }
}
