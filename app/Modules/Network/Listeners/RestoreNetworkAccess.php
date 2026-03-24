<?php

namespace Modules\Network\Listeners;

use Modules\Billing\Events\InvoicePaid;
use Modules\Network\Services\RadiusManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class RestoreNetworkAccess implements ShouldQueue
{
    public string $queue = 'network';

    public function __construct(
        protected RadiusManager $radiusManager
    ) {}

    public function handle(InvoicePaid $event): void
    {
        $customer = $event->invoice->customer;

        // Orchestrate: Only restore if the customer has no other overdue invoices
        if (!$customer->hasOverdueInvoices()) {
            $this->radiusManager->resumeRadiusAccount($customer->radiusAccount);
        }
    }
}