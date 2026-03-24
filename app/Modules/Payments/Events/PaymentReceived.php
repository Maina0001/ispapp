<?php

namespace Modules\Payments\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\MpesaTransaction;

class PaymentReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(public MpesaTransaction $transaction) {}
}