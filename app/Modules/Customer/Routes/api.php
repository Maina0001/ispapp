<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Onboarding
    Route::post('customers/onboard', 'Modules\Customer\Http\Controllers\Api\V1\OnboardingController@onboard')->name('api.v1.customers.onboard');

    // Flat REST Routes
    Route::get('customers', 'Modules\Customer\Http\Controllers\Api\V1\CustomerController@index')->name('api.v1.customers.index');
    Route::post('customers', 'Modules\Customer\Http\Controllers\Api\V1\CustomerController@store')->name('api.v1.customers.store');
    Route::get('customers/{customer}', 'Modules\Customer\Http\Controllers\Api\V1\CustomerController@show')->name('api.v1.customers.show');
    Route::put('customers/{customer}', 'Modules\Customer\Http\Controllers\Api\V1\CustomerController@update')->name('api.v1.customers.update');
    Route::delete('customers/{customer}', 'Modules\Customer\Http\Controllers\Api\V1\CustomerController@destroy')->name('api.v1.customers.destroy');

    // Actions
    Route::post('customers/{customer}/suspend', 'Modules\Customer\Http\Controllers\Api\V1\CustomerStatusController@suspend')->name('api.v1.customers.suspend');
    Route::post('customers/{customer}/reactivate', 'Modules\Customer\Http\Controllers\Api\V1\CustomerStatusController@reactivate')->name('api.v1.customers.reactivate');
    Route::get('customers/{customer}/usage', 'Modules\Customer\Http\Controllers\Api\V1\CustomerController@usage')->name('api.v1.customers.usage');
    Route::get('customers/{customer}/billing-history', 'Modules\Customer\Http\Controllers\Api\V1\CustomerController@billingHistory')->name('api.v1.customers.billing-history');

    // Payment Polling
    Route::get('customer/payment-status/{checkoutId}', 'Modules\Customer\Http\Controllers\Api\V1\StatusController@checkPayment')->name('api.v1.customer.payment_status');

});
