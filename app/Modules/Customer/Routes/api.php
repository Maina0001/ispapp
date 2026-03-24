<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Api\V1\CustomerController;
use Modules\Customer\Http\Controllers\Api\V1\CustomerStatusController;

/**
 * Customer Module API V1 Routes
 * Prefix: /api/v1/customers
 */
Route::prefix('v1')->group(function () {

    // Standard RESTful API Resource
    // Provides: GET (index/show), POST (store), PUT/PATCH (update), DELETE (destroy)
    Route::apiResource('customers', CustomerController::class);

    // Domain-Specific Actions (RPC Style)
    Route::prefix('customers/{customer}')->group(function () {
        // Handle customer lifecycle states
        Route::post('suspend', [CustomerStatusController::class, 'suspend'])->name('customers.suspend');
        Route::post('reactivate', [CustomerStatusController::class, 'reactivate'])->name('customers.reactivate');
        
        // Fetch module-specific related data (sub-resources)
        Route::get('usage', [CustomerController::class, 'usage'])->name('customers.usage');
        Route::get('billing-history', [CustomerController::class, 'billingHistory'])->name('customers.billing-history');
    });

});