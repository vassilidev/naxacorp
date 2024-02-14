<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function systemInfo(): View
    {
        $laravelVersion = app()->version();
        $timeZone = config('app.timezone');
        $pageTitle = 'Application Information';

        return view('admin.system.info', compact('pageTitle', 'laravelVersion', 'timeZone'));
    }

    public function optimize(): View
    {
        $pageTitle = 'Clear System Cache';

        return view('admin.system.optimize', compact('pageTitle'));
    }

    public function optimizeClear()
    {
        Artisan::call('optimize:clear');
        $notify[] = ['success', 'Cache cleared successfully'];

        return back()->withNotify($notify);
    }

    public function systemServerInfo(): View
    {
        $currentPHP = phpversion();
        $pageTitle = 'Server Information';
        $serverDetails = $_SERVER;

        return view('admin.system.server', compact('pageTitle', 'currentPHP', 'serverDetails'));
    }
}
