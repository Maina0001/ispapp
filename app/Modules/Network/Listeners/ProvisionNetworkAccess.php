<?php

namespace Modules\Network\Listeners;

use Modules\Billing\Events\SubscriptionCreated;
use Modules\Network\Services\ProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProvisionNetworkAccess implements ShouldQueue
{
    public string $queue = 'network';

    public function __construct(
        protected ProvisioningService $provisioningService
    ) {}

    public function handle(SubscriptionCreated $event): void
    {
        // Orchestrate: Pass the customer and plan details to the provisioning engine
        $this->provisioningService->provisionCustomerService(
            $event->subscription->customer,
            $event->subscription->plan
        );
    }
}