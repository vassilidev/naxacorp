<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $guard = 'admin'): Response
    {
        if (! Auth::guard($guard)->check()) {
            return to_route('admin.login');
        }

        return $next($request);
    }
}
