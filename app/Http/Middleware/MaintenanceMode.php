<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $general = gs();
        if ($general->maintenance_mode == 1) {

            if ($request->is('api/*')) {
                $notify[] = 'Our application is currently in maintenance mode';

                return response()->json([
                    'remark' => 'maintenance_mode',
                    'status' => 'error',
                    'message' => ['error' => $notify],
                ]);
            } else {
                return to_route('maintenance');
            }
        }

        return $next($request);
    }
}
