<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Web\PortalController;

/**
 * Captive Portal - Web Routes
 * These routes are accessible without internet (Walled Garden)
 */
Route::group([
    'prefix' => 'portal',
    'middleware' => ['web', 'tenant.resolve'] // tenant.resolve identifies the ISP/NAS context
], function () {

    // --- View Routes ---
    
    // Landing Page: Entry point (detects MAC/IP from MikroTik)
    Route::get('/', [PortalController::class, 'index'])->name('portal.home');

    // Catalogue: Displays the Sh5, Sh10, etc. cards from your screenshot
    Route::get('/plans', [PortalController::class, 'plans'])->name('portal.plans');

    // Voucher: Form to enter a physical/SMS voucher code
    Route::get('/voucher', [PortalController::class, 'voucher'])->name('portal.voucher');

    // Success: Post-payment polling and auto-login redirection
    Route::get('/success', [PortalController::class, 'success'])->name('portal.success');

    // Error: Handling failed STK Pushes or expired sessions
    Route::get('/error', [PortalController::class, 'error'])->name('portal.error');


    // --- Action Routes ---

    // Reconnect: For users with an active session who moved between APs
    Route::post('/reconnect', [PortalController::class, 'reconnect'])->name('portal.reconnect');

    // Free Trial: Logic for the 07:00 -> 09:00 daily window
    Route::post('/free-trial', [PortalController::class, 'freeTrial'])->name('portal.free-trial');

});