<?php

namespace Modules\Payments\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Payments\Services\PaymentService;

class SyncWithPaymentGateway implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $tenant_id = null) {}

    public function handle(PaymentService $paymentService): void
    {
        // Logic for reconciling gateway batch reports
        $paymentService->syncGatewayTransactions();
    }
}