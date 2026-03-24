<?php

use Illuminate\Support\Facades\Route;
use Modules\Network\Http\Controllers\Web\NetworkExportController;
use Modules\Network\Http\Controllers\Web\NetworkStatusController;

/**
 * Network Module Web Routes
 * Handles downloads, exports, and public diagnostic views.
 */
Route::prefix('network')->group(function () {

    // Export router configurations (e.g., MikroTik setup scripts)
    Route::get('export/config/{nas}', [NetworkExportController::class, 'downloadConfig'])
        ->name('network.web.export-config');

    // Public Network Status Page (e.g., status.isp.com)
    Route::get('status-page', [NetworkStatusController::class, 'index'])
        ->name('network.web.status');

});