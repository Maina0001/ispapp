<?php

namespace Modules\Payments\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\MpesaTransaction;

/**
 * Triggered when an M-Pesa transaction is fully reconciled and verified.
 */
class MpesaTransactionVerified
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public MpesaTransaction $transaction)
    {
        $this->tenant_id = $transaction->tenant_id;
    }
}