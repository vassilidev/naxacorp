<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminPasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
        |--------------------------------------------------------------------------
        | Password Reset Controller
        |--------------------------------------------------------------------------
        |
        | This controller is responsible for handling password reset requests
        | and uses a simple trait to include this behavior. You're free to
        | explore this trait and override any methods you wish to tweak.
        |
        */

    use ResetsPasswords;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('admin.guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function showResetForm(Request $request, $token)
    {
        $pageTitle = 'Account Recovery';
        $resetToken = AdminPasswordReset::where('token', $token)->where('status', Status::ENABLE)->first();

        if (! $resetToken) {
            $notify[] = ['error', 'Verification code mismatch'];

            return to_route('admin.password.reset')->withNotify($notify);
        }
        $email = $resetToken->email;

        return view('admin.auth.passwords.reset', compact('pageTitle', 'email', 'token'));
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:4',
        ]);

        $reset = AdminPasswordReset::where('token', $request->token)->orderBy('created_at', 'desc')->first();
        $admin = Admin::where('email', $reset->email)->first();
        if ($reset->status == Status::DISABLE) {
            $notify[] = ['error', 'Invalid code'];

            return to_route('admin.login')->withNotify($notify);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();
        $reset->status = Status::DISABLE;
        $reset->save();

        $ipInfo = getIpInfo();
        $browser = osBrowser();
        notify($admin, 'PASS_RESET_DONE', [
            'operating_system' => $browser['os_platform'],
            'browser' => $browser['browser'],
            'ip' => $ipInfo['ip'],
            'time' => $ipInfo['time'],
        ], ['email'], false);

        $notify[] = ['success', 'Password changed'];

        return to_route('admin.login')->withNotify($notify);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admins');
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }
}
