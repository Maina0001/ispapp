<?php

namespace App\Modules\Network\Services;

use App\Modules\Network\Models\RadiusAccount;
use App\Modules\Network\Models\ServicePlan;

class BandwidthManager
{
    public function assignBandwidthProfile(RadiusAccount $account, ServicePlan $plan): void
    {
        // 1. Logic: MikroTik expects specific format (e.g., 5M/10M)
        // We use floor to avoid decimals in the rate-limit string which can crash some RouterOS versions
        $upload = floor($plan->upload_speed / 1024);
        $download = floor($plan->download_speed / 1024);
        $rateLimit = "{$upload}M/{$download}M";
        
        // 2. Logic: Update or Create the RADIUS attribute
        // NOTE: 'MikroTik-Rate-Limit' is case-sensitive in many RADIUS dictionaries.
        // NOTE: The operator should be ':=' (Set) rather than '=' (Check) for attributes sent to the router.
        $account->attributes()->updateOrCreate(
            ['attribute' => 'MikroTik-Rate-Limit'],
            [
                'value' => $rateLimit, 
                'op' => ':=',
                'tenant_id' => $account->tenant_id // Ensure the attribute inherits the account's tenant
            ]
        );
    }

    public function updateBandwidthProfile(RadiusAccount $account, ServicePlan $newPlan): void
    {
        $this->assignBandwidthProfile($account, $newPlan);
    }
}