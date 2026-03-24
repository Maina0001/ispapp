<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\Api\V1\PaymentController;
use Modules\Payments\Http\Controllers\Api\V1\MpesaTransactionController;

/**
 * Payments Module API V1 Routes
 * Prefix: /api/v1/payments
 */
Route::prefix('v1/payments')->group(function () {

    // Standard Payment Records (Read-only for history, Store for manual recording)
    // Provides: GET (index/show), POST (store)
    Route::apiResource('transactions', PaymentController::class)
        ->names('payments.transactions')
        ->only(['index', 'show', 'store']);

    // M-Pesa Specific Actions
    Route::prefix('mpesa')->group(function () {
        // Trigger STK Push for a specific invoice
        Route::post('stk-push', [PaymentController::class, 'initiateStkPush'])
            ->name('payments.mpesa.stk');

        // Query status of a specific checkout request
        Route::get('query/{checkoutRequestId}', [MpesaTransactionController::class, 'queryStatus'])
            ->name('payments.mpesa.query');
            
        // List all M-Pesa specific logs/attempts
        Route::get('logs', [MpesaTransactionController::class, 'index'])
            ->name('payments.mpesa.logs');
    });

});