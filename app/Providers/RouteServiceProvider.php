<?php

namespace App\Providers;

use App\Http\Controllers\SiteController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Laramin\Utility\VugiChugi;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware(VugiChugi::mdNm())->group(function () {
                Route::prefix('api')
                    ->middleware(['api', 'maintenance'])
                    ->group(base_path('routes/api.php'));

                Route::middleware(['web', 'maintenance'])
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));

                Route::middleware(['web'])
                    ->prefix('manageme')
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));

                Route::middleware(['web'])
                    ->prefix('staff')
                    ->name('staff.')
                    ->group(base_path('routes/branch_staff.php'));

                Route::middleware(['web', 'maintenance'])
                    ->prefix('user')
                    ->group(base_path('routes/user.php'));

                Route::middleware(['web', 'maintenance'])->group(base_path('routes/web.php'));
            });
        });

        Route::get('maintenance-mode', [App\Http\Controllers\SiteController::class, 'maintenance'])->name('maintenance');
    }
}
