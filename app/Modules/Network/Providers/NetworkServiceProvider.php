<?php

namespace App\Modules\Network\Providers;

use Illuminate\Support\ServiceProvider;

class NetworkServiceProvider extends ServiceProvider
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
        // This is the "Automated Fix"
        // It tells Laravel to include this folder when running 'php artisan migrate'
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}