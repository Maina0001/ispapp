<?php

namespace Modules\Payments\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Payments\Models\Payment;

/**
 * Represents the intent of a customer to make a payment.
 */
class PaymentInitiated
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    /**
     * @param Payment $payment The pending payment record.
     */
    public function __construct(public Payment $payment)
    {
        $this->tenant_id = $payment->tenant_id;
    }
}