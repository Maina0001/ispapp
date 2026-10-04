<?php

namespace Modules\Billing\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Billing\Models\Subscription;
use Modules\Network\Services\ProvisioningService;

class CheckExpiredSubscriptions implements ShouldQueue
{
    use Queueable;

    public function handle(ProvisioningService $network)
    {
        $expired = Subscription::where('expires_at', '<', now())
            ->where('status', 'active')
            ->get();

        foreach ($expired as $subscription) {
            // 1. Update Billing Status
            $subscription->update(['status' => 'expired']);

            // 2. Tell Network Module to disconnect (Decoupled call)
            $network->deprovisionCustomerService($subscription->customer);
        }
    }
}