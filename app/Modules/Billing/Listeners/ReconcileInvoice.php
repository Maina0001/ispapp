<?php

namespace Modules\Billing\Listeners;

use Modules\Payments\Events\PaymentReceived;
use Modules\Billing\Services\BillingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReconcileInvoice implements ShouldQueue
{
    public string $queue = 'billing';

    public function __construct(
        protected BillingService $billingService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        // Orchestrate: Tell the billing service to apply this payment to the ledger
        $this->billingService->applyPaymentToInvoices($event->payment);
    }
}