<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('hexagon-domain-search', function (Request $request) {
            $user = $request->user();
            if ($user) {
                return Limit::perMinute(60)
                    ->by('user:' . $user->id);
            }
            return Limit::perMinute(30)
                ->by('ip:' . $request->ip());
        });
    }   
}
