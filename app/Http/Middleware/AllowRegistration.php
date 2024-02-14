<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;

class AllowRegistration
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $general = gs();
        if ($general->registration == 0) {
            return to_route('registration.disabled');
        }

        return $next($request);
    }
}
