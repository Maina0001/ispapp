<?php

use Illuminate\Support\Facades\Route;
use Modules\Billing\Http\Controllers\Web\InvoiceDownloadController;
use Modules\Billing\Http\Controllers\Web\BillingStatementController;

/**
 * Billing Module Web Routes
 * Handles document generation and customer statements.
 */
Route::prefix('billing')->group(function () {

    // PDF Invoice Downloads (usually signed URLs for security)
    Route::get('invoices/{invoice}/download', [InvoiceDownloadController::class, 'download'])
        ->name('billing.web.invoice-download');

    // View Customer Statement (Printable HTML view)
    Route::get('statements/{customer}', [BillingStatementController::class, 'show'])
        ->name('billing.web.statement');

});