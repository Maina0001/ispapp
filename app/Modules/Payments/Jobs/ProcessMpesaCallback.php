<?php

namespace Modules\Payments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Payments\Events\PaymentCompleted;
use Modules\Payments\Models\MpesaTransaction;

class ProcessMpesaCallback implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(protected MpesaTransaction $transaction) {}

    public function handle()
    {
        $payload = $this->transaction->raw_payload;
        $resultCode = data_get($payload, 'Body.stkCallback.ResultCode');

        if ($resultCode === 0) {
            // 1. Update Internal Status
            $this->transaction->update(['status' => 'completed']);

            // 2. Trigger the Global System Event
            // The Billing module will hear this and clear the Invoice.
            // The Network module will hear this and open the MikroTik gate.
            event(new PaymentCompleted($this->transaction));
        } else {
            $this->transaction->update(['status' => 'failed']);
        }
    }
}