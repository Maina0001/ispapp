<?php

namespace Modules\Payments\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\Payment;

/**
 * Indicates that a payment attempt was unsuccessful.
 */
class PaymentFailed
{
    use SerializesModels;

    public ?int $tenant_id;

    /**
     * @param Payment $payment The failed payment record.
     * @param string $reason The error message or failure code.
     */
    public function __construct(
        public Payment $payment,
        public string $reason
    ) {
        $this->tenant_id = $payment->tenant_id;
    }
}