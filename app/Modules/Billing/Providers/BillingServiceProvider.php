<?php

namespace App\Modules\Billing\Providers;

use Illuminate\Support\ServiceProvider;

class BillingServiceProvider extends ServiceProvider
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
        // It tells Laravel to look for migrations inside this module's directory
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
