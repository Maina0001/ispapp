<?php

namespace Modules\Customer\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CustomerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Load standard Web routes (Blade views)
        if (file_exists(__DIR__ . '/../Routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        }

        // 2. Load Versioned API routes
        $this->registerApiRoutes();

        // 3. Load Migrations
        if (is_dir(__DIR__ . '/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }
        
        // 4. Load Portal Views
        if (is_dir(__DIR__ . '/../Resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'customer');
        }
    }

    protected function registerApiRoutes(): void
    {
        $apiPath = __DIR__ . '/../Routes/api.php';
        if (file_exists($apiPath)) {
            Route::prefix('api')
                ->middleware('api') 
                ->group($apiPath);
        }
    }
}