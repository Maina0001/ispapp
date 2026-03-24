<?php

namespace App\Modules\Reporting\Providers;

use Illuminate\Support\ServiceProvider;

class ReportingServiceProvider extends ServiceProvider
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
        // It tells Laravel to include this directory when running 'php artisan migrate'
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}