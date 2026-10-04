<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Api\V1\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider.
|
*/

// 1. Redirect the root URL to the portal
Route::get('/', function () {
    return redirect()->route('portal.plans');
});

// 2. Simple Health Check (Useful for testing Apache)
Route::get('/status', function () {
    return response()->json([
        'status' => 'online',
        'isp' => 'Kariuki Highspeed Services',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Note: Your /portal route is likely defined in 
// app/Modules/Customer/Routes/web.php
