<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\Web\MpesaWebhookController;

/**
 * Payments Module Web Routes
 * Used primarily for external callbacks and 3rd party redirects.
 */
Route::prefix('payments/hooks')->group(function () {

    // M-Pesa Daraja Callback
    // Note: This route must be added to the VerifyCsrfToken middleware $except array
    Route::post('mpesa/{tenant_id?}', [MpesaWebhookController::class, 'handleCallback'])
        ->name('payments.webhook.mpesa');

    // Success/Failure redirect pages for hosted checkout (if applicable)
    Route::get('callback/success', [MpesaWebhookController::class, 'success'])->name('payments.callback.success');
    Route::get('callback/failure', [MpesaWebhookController::class, 'failure'])->name('payments.callback.failure');

});