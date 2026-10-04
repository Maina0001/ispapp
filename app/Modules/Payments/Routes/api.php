<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\MpesaController;

/**
 * Payments Module API V1 Routes
 */
Route::prefix('v1/payments')->group(function () {

    // M-Pesa Specific Actions Group
    Route::prefix('mpesa')->group(function () {
        
        // Line 14: Maps to POST /api/v1/payments/mpesa/stk-push
        Route::post('stk-push', [MpesaController::class, 'stkPush'])
            ->name('payments.mpesa.stk');
            
        // Line 17-18: Maps to POST /api/v1/payments/mpesa/callback
        Route::post('callback', [MpesaController::class, 'callback'])
            ->name('payments.mpesa.callback');
    });

});