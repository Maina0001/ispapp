<?php

namespace Modules\Payments\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\Payment;
use illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class PaymentReceived implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;
    public ?int $tenant_id;

    public function __construct(public Payment $payment) {
        $this->tenant_id = $payment->tenant_id;
    }
} 