<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the M-Pesa API integration.
    | You can set your M-Pesa credentials and environment here.
    |
    */

    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    'shortcode' => env('MPESA_SHORTCODE', ''),
    'passkey' => env('MPESA_PASSKEY', ''),
    'environment' => env('MPESA_ENV', 'sandbox'), // Options: 'sandbox' or 'production'
    'callback_url' => env('MPESA_CALLBACK_URL', ''), // URL to receive payment notifications
];