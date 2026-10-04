<?php

namespace Modules\Network\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class NetworkServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Internal Technical API routes (Auth & Tenant Required)
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware(['api', 'auth:sanctum', 'tenant.resolve'])
            ->group(__DIR__ . '/../Routes/api.php');
    }

    /**
     * External/Administrative Web routes
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}