<?php

namespace Modules\Payments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Payments\Events\MpesaTransactionCompleted;
use Modules\Payments\Models\MpesaTransaction;
use illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMpesaCallback implements ShouldQueue
{
    use Dispatchable, interactsWithQueue, Queueable, SerializesModels;
    public int $tries = 3;

    public function __construct(protected MpesaTransaction $transaction) {}

    public function handle()
    {
        $payload = $this->transaction->raw_payload;
        $resultCode = data_get($payload, 'Body.stkCallback.ResultCode');

        if ((int)$resultCode === 0) {
            // 1. Update Internal Status
            $this->transaction->update(['status' => 'completed']);{
                return;
            }

            // 2. Trigger the Global System Event
            // The Billing module will hear this and clear the Invoice.
            // The Network module will hear this and open the MikroTik gate.
            event(new MpesaTransactionCompleted($this->transaction));
        } else {
            $this->transaction->update(['status' => 'failed']);
        }
    }
}