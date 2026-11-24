<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RateLimiter::for('himatif-login', static function (Request $request): Limit {
            $email = mb_strtolower(trim((string) $request->input('email')));

            return Limit::perMinute(5)->by(hash('sha256', $email.'|'.$request->ip()));
        });

        RateLimiter::for('himatif-register', static function (Request $request): Limit {
            return Limit::perHour(5)->by($request->ip());
        });
    }
}
