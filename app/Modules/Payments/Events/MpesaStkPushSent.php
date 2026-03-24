<?php

namespace Modules\Payments\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\MpesaTransaction;

/**
 * Triggered when the STK Push request is successfully handed off to Safaricom.
 */
class MpesaStkPushSent
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public MpesaTransaction $transaction)
    {
        $this->tenant_id = $transaction->tenant_id;
    }
}