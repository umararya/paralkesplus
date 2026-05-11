<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\UsernameUserProvider;

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
        // Daftarkan custom provider untuk login via username
        Auth::provider('username-eloquent', function ($app, array $config) {
            return new UsernameUserProvider(
                $app['hash'],
                $config['model']
            );
        });
    }
}