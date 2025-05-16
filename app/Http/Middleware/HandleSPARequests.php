<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSPARequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request is for an API route
        if ($request->is('api/*')) {
            return $next($request);
        }

        // Check if the request is for an admin route
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Check if the request is for a static asset or file
        if ($this->isStaticAsset($request)) {
            return $next($request);
        }

        // For all other frontend routes, return the SPA view
        return response()->view('app');
    }

    /**
     * Determine if the request is for a static asset.
     */
    protected function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        
        // Define common static asset extensions
        $staticExtensions = [
            'js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 
            'woff', 'woff2', 'ttf', 'eot', 'map', 'json'
        ];
        
        // Check if the path ends with any of the static asset extensions
        foreach ($staticExtensions as $extension) {
            if (str_ends_with(strtolower($path), '.' . $extension)) {
                return true;
            }
        }
        
        return false;
    }
}
