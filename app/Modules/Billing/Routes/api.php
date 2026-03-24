<?php

use Illuminate\Support\Facades\Route;
use Modules\Billing\Http\Controllers\Api\V1\InvoiceController;
use Modules\Billing\Http\Controllers\Api\V1\SubscriptionController;
use Modules\Billing\Http\Controllers\Api\V1\PlanController;
use Modules\Billing\Http\Controllers\Api\V1\CreditNoteController;

/**
 * Billing Module API V1 Routes
 * Prefix: /api/v1/billing
 */
Route::prefix('v1/billing')->group(function () {

    // Core Invoicing Logic
    Route::apiResource('invoices', InvoiceController::class)
        ->names('billing.invoices');

    // Recurring Subscriptions
    Route::apiResource('subscriptions', SubscriptionController::class)
        ->names('billing.subscriptions');

    // Service Plans / Pricing Tiers
    Route::apiResource('plans', PlanController::class)
        ->names('billing.plans');

    // Financial Adjustments
    Route::apiResource('credit-notes', CreditNoteController::class)
        ->names('billing.credit-notes')
        ->only(['index', 'show', 'store']);

    // Specialized Billing Operations
    Route::prefix('operations')->group(function () {
        // Trigger manual invoice generation for a customer
        Route::post('invoices/generate/{customer}', [InvoiceController::class, 'generateManual'])
            ->name('billing.ops.generate');

        // Batch process monthly renewals
        Route::post('subscriptions/run-billing-cycle', [SubscriptionController::class, 'runCycle'])
            ->name('billing.ops.run-cycle');

        // Apply a discount code to a subscription
        Route::post('subscriptions/{subscription}/apply-discount', [SubscriptionController::class, 'applyDiscount'])
            ->name('billing.ops.discount');
    });
});