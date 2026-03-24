<?php

namespace App\Modules\Customer\Providers;

use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
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
        // This is the "Permanent Fix"
        // It tells Laravel to include this directory when scanning for migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
