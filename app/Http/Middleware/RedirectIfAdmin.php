<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $guard = 'admin'): Response
    {
        if (Auth::guard($guard)->check()) {
            return to_route('admin.dashboard');
        }

        return $next($request);
    }
}
