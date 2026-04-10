<?php

namespace Modules\Customer\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CustomerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1. Load standard Web routes (Blade views)
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // 2. Load Versioned API routes
        $this->registerApiRoutes();

        // 3. Load Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        // Now you can use view('customer::portal.home')
    $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'customer');
    }

    protected function registerApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api') // Applies rate limiting and JSON headers
            ->group(__DIR__ . '/../Routes/api.php');
    }
}