<?php

namespace App\Modules\Payments\Models;

use App\Core\Abstract\BaseModel;

class PaymentGateway extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'name',           // 'Mpesa_Main', 'Equity_Bank'
        'provider',       // 'mpesa', 'stripe', 'equity'
        'credentials',    // JSON: consumer_key, consumer_secret, shortcode, etc.
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array', // Always encrypt API keys in the DB
        'is_active' => 'boolean',
    ];
}