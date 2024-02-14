<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountOfficer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, $guard = 'branch_staff'): Response
    {
        if (Auth::guard($guard)->user()->designation != Status::ROLE_ACCOUNT_OFFICER) {
            return to_route('staff.dashboard');
        }

        return $next($request);
    }
}
