<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // 1. Fix for older MySQL/MariaDB versions (Optional but recommended)
        Schema::defaultStringLength(191);

        // 2. Register Modular Migrations
        // This ensures 'php artisan migrate' finds your ISP system tables
        $modulePath = base_path('app/Modules');

        if (is_dir($modulePath)) {
            $modules = array_diff(scandir($modulePath), ['.', '..']);

            foreach ($modules as $module) {
                $migrationPath = "$modulePath/$module/Database/Migrations";
                
                if (is_dir($migrationPath)) {
                    $this->loadMigrationsFrom($migrationPath);
                }
            }
        }

        /* * 3. Optional: Register Module Views/Translations 
         * If you have custom portal pages in your modules, 
         * uncomment the logic below to load them automatically.
         */
        /*
        foreach ($modules as $module) {
            $viewPath = "$modulePath/$module/Resources/views";
            if (is_dir($viewPath)) {
                $this->loadViewsFrom($viewPath, strtolower($module));
            }
        }
        */
    }
}