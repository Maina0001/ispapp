<?php

namespace Modules\Payments\Providers; // Strict Module Namespace

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Place database/interface repository bindings here if needed
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Load Module Migrations (keeps database tables modular)
        if (is_dir(__DIR__ . '/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }

        // 2. Load Module API and Web Routes
        $this->registerRoutes();
    }

    /**
     * Register the module's routes.
     */
    protected function registerRoutes(): void
    {
        $apiRoutes = __DIR__ . '/../Routes/api.php';

        if (file_exists($apiRoutes)) {
            Route::prefix('api')
                ->middleware('api')
                ->group($apiRoutes);
        }
    }
}