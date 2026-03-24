<?php

namespace Modules\Billing\Listeners;

use Modules\Network\Events\ServiceDeprovisioned;
use Modules\Billing\Services\BillingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelSubscriptions implements ShouldQueue
{
    public string $queue = 'billing';

    public function __construct(
        protected BillingService $billingService
    ) {}

    public function handle(ServiceDeprovisioned $event): void
    {
        // Orchestrate: Terminate recurring billing cycles for this customer
        $this->billingService->cancelAllCustomerSubscriptions($event->customer);
    }
}