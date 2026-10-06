<?php

namespace Modules\Payments\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\MpesaTransaction;

class MpesaTransactionFailed implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(
        public MpesaTransaction $transaction,
        public string $reason = 'unknown',
    ) {
        $this->tenant_id = $transaction->tenant_id;
    }
}