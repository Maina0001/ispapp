<?php

namespace Modules\Payments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\MpesaTransaction;
use Modules\Payments\Services\MpesaService;

class VerifyMpesaTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public MpesaTransaction $transaction)
    {
        $this->tenant_id = $transaction->tenant_id;
    }

    public function handle(MpesaService $mpesaService): void
    {
        $mpesaService->verifyTransaction($this->transaction->checkout_request_id);
    }
}