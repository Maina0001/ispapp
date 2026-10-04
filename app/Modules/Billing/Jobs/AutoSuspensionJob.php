<?php

namespace Modules\Billing\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Billing\Models\Subscription;
use Modules\Network\Services\ProvisioningService;
use Illuminate\Support\Facades\Log;

class AutoSuspensionJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * We inject the ProvisioningService so this job can talk to the MikroTik/Radius
     */
    public function handle(ProvisioningService $networkService)
    {
        // 1. Find all active subscriptions that have expired
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredSubscriptions as $sub) {
            // 2. Update database status
            $sub->update(['status' => 'expired']);

            // 3. Tell the Network Module to deprovision (Kick from MikroTik)
            // This calls the deprovisionCustomerService method we wrote earlier!
            $networkService->deprovisionCustomerService($sub->customer);

            Log::info("Auto-Suspended: MAC {$sub->customer->mac_address} - Time Expired.");
        }
    }
}