<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KycMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($request->is('api/*') && ($user->kv == Status::KYC_UNVERIFIED || $user->kv == Status::KYC_PENDING)) {
            $notify[] = 'You are unable to withdraw due to KYC verification';

            return response()->json([
                'remark' => 'kyc_verification',
                'status' => 'error',
                'message' => ['error' => $notify],
            ]);
        }
        if ($user->kv == 0) {
            $notify[] = ['error', 'You are not KYC verified. For being KYC verified, please provide these information'];

            return to_route('user.kyc.form')->withNotify($notify);
        }
        if ($user->kv == 2) {
            $notify[] = ['warning', 'Your documents for KYC verification is under review. Please wait for admin approval'];

            return to_route('user.home')->withNotify($notify);
        }

        return $next($request);
    }
}
