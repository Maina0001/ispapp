<?php

namespace Modules\Payments\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Payments\Interfaces\PaymentInitiatorInterface;
use Modules\Payments\Services\MpesaInitiationService;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * This is where the Interface is bound to the Implementation.
     */
    public function register(): void
    {
        // Whenever the app asks for the Initiator Interface, give them M-Pesa.
        // To switch to Airtel, you only change the second class name here!
        $this->app->bind(
            PaymentInitiatorInterface::class,
            MpesaInitiationService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Load modular migrations (Transactions, Webhooks logs, etc.)
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }

        // 2. Load API Routes (For Webhooks/Callbacks)
        $this->registerRoutes();
    }

    /**
     * Register the routes for the payments module.
     */
    protected function registerRoutes(): void
    {
        Route::prefix('api/payments')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}