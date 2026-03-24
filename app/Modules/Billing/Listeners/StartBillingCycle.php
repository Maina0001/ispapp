<?php

namespace Modules\Billing\Listeners;

use Modules\Network\Events\RadiusAccountCreated;
use Modules\Billing\Services\BillingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class StartBillingCycle implements ShouldQueue
{
    public string $queue = 'billing';

    public function __construct(
        protected BillingService $billingService
    ) {}

    public function handle(RadiusAccountCreated $event): void
    {
        // Orchestrate: Activate the subscription billing cycle based on hardware setup time
        $this->billingService->activateSubscriptionBilling(
            $event->radiusAccount->customer,
            now()
        );
    }
}