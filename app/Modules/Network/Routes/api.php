<?php

use Illuminate\Support\Facades\Route;
use Modules\Network\Http\Controllers\Api\V1\NasController;
use Modules\Network\Http\Controllers\Api\V1\IpPoolController;
use Modules\Network\Http\Controllers\Api\V1\RadiusAccountController;
use Modules\Network\Http\Controllers\Api\V1\BandwidthProfileController;
use App\Modules\Network\Http\Controllers\Api\V1\StatusController;
/**
 * Network Module API V1 Routes
 * Prefix: /api/v1/network
 */

    Route::get('/status', [StatusController::class, 'checkStatus']);


Route::prefix('v1/network')->group(function () {

    // Infrastructure Management (Routers & Hardware)
    Route::apiResource('nas', NasController::class)
        ->names('network.nas');

    Route::get('/status', [StatusController::class, 'checkStatus']);    

    // IP Address Management (Subnets & Pools)
    Route::apiResource('ip-pools', IpPoolController::class)
        ->names('network.ip-pools');

    // Service Tier Management (Speed Plans)
    Route::apiResource('bandwidth-profiles', BandwidthProfileController::class)
        ->names('network.bandwidth');

    // RADIUS Account Lifecycle & Active Sessions
    Route::apiResource('radius-accounts', RadiusAccountController::class)
        ->names('network.radius');

    // Domain-Specific Network Operations
    Route::prefix('operations')->group(function () {
        // Force disconnect a user (PoD - Packet of Disconnect)
        Route::post('disconnect/{username}', [RadiusAccountController::class, 'disconnect'])
            ->name('network.ops.disconnect');

        // Sync local profiles to the RADIUS database
        Route::post('sync-radius', [NasController::class, 'sync'])
            ->name('network.ops.sync');
            
        // Real-time ping/status check for a NAS device
        Route::get('nas/{nas}/status', [NasController::class, 'checkStatus'])
            ->name('network.nas.status');



    
    });
});