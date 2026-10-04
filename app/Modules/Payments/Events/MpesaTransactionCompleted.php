<?php

namespace Modules\Payments\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\MpesaTransaction;

/**
 * Fired when Safaricom confirms a successful M-Pesa payment.
 *
 * Trigger for the downstream chain: creates a Payment, applies it to
 * invoices, and provisions the customer's network access.
 */
class MpesaTransactionCompleted implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public MpesaTransaction $transaction)
    {
        $this->tenant_id = $transaction->tenant_id;
    }
}