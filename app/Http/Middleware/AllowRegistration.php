<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
