<?php

namespace Modules\Billing\Services;

use Modules\Billing\Models\Subscription;
use Modules\Network\Services\ProvisioningService;

class SuspensionService
{
    public function __construct(protected ProvisioningService $network) {}

    public function suspendSubscription(Subscription $subscription): void
    {
        $subscription->update(['status' => 'suspended', 'suspended_at' => now()]);
        
        // Push to Network Module to update MikroTik/Radius
        $this->network->suspendAccess($subscription->customer);
    }

    public function restoreSubscription(Subscription $subscription): void
    {
        $subscription->update(['status' => 'active', 'suspended_at' => null]);
        
        // Re-provision on the router
        $this->network->activateAccess($subscription->customer);
    }
}