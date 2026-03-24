<?php

namespace Modules\Payments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Payments\Models\Payment;
use Modules\Payments\Services\PaymentService;

class ApplyPaymentToInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Payment $payment)
    {
        $this->tenant_id = $payment->tenant_id;
    }

    public function handle(PaymentService $paymentService): void
    {
        $paymentService->applyPaymentToInvoice($this->payment);
    }
}