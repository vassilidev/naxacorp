<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class CheckModule
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $name): Response
    {
        $general = gs();

        if ($general->modules->$name) {
            return $next($request);
        } else {
            $notify[] = 'Sorry '.keyToTitle($name).' is not available now';

            if ($request->is('api/*')) {
                return response()->json([
                    'remark' => 'module_disable_error',
                    'status' => 'error',
                    'message' => ['error' => $notify],
                ]);
            }

            abort(404);
        }
    }
}
