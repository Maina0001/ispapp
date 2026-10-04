<?php

namespace Modules\Payments\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Payments\Models\Payment;
use Modules\Payments\Models\MpesaTransaction;
use Modules\Tenants\Models\Tenant;

/**
 * Represents the intent of a customer to make a payment.
 */
class PaymentInitiated
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    /**
     * @param MpesaTransaction $transaction The pending M-Pesa transaction record.
     */
    public function __construct(public MpesaTransaction $transaction)
    {
        $this->tenant_id = $transaction->tenant_id;
    }
}