<?php

namespace App\Modules\Payments\Models;

use App\Core\Abstract\BaseModel;

class MpesaTransaction extends BaseModel
{
    protected $fillable = [
        'tenant_id',
        'merchant_request_id',
        'checkout_request_id',
        'result_code',
        'result_desc',
        'amount',
        'mpesa_receipt_number',
        'transaction_date',
        'phone_number',
        'status', // 'initiated', 'success', 'failed'
    ];

    /**
     * Scope to find by checkout request ID (used in webhooks)
     */
    public function scopeByCheckoutId($query, string $id)
    {
        return $query->where('checkout_request_id', $id);
    }
}