<?php

namespace App\Modules\Network\Services;

use App\Core\Abstract\BaseService;
use App\Modules\Customer\Models\Customer;
use App\Modules\Network\Jobs\SyncToRouterJob;
use App\Modules\Network\Events\ServiceProvisioned;
use Illuminate\Support\Facades\Log;

class ProvisioningService extends BaseService
{
    public function __construct(
        protected RadiusManager $radiusManager,
        protected BandwidthManager $bandwidthManager
    ) {}

    public function provisionCustomerService(Customer $customer, $plan): void
    {
        $this->transactional(function () use ($customer, $plan) {
            // 1. Ensure Radius Account exists
            // CRITICAL: Use MAC address as the 'password' for seamless Hotspot login
            $account = $this->radiusManager->createRadiusAccount($customer, $customer->mac_address);

            // 2. Map bandwidth profile (Sets MikroTik-Rate-Limit in radreply)
            $this->bandwidthManager->assignBandwidthProfile($account, $plan);

            // 3. Dispatch Background Job
            // We pass the tenant_id explicitly so the RestoreTenantContext middleware can pick it up
            SyncToRouterJob::dispatch($customer, $plan, 'provision', $customer->tenant_id)
                ->onQueue('network');

            event(new ServiceProvisioned($customer, $plan));
            
            Log::info("Provisioned Hotspot access for MAC: {$customer->mac_address}");
        });
    }

    public function deprovisionCustomerService(Customer $customer): void
    {
        $this->transactional(function () use ($customer) {
            $account = $customer->radiusAccount;
            
            if ($account) {
                $this->radiusManager->suspendRadiusAccount($account);
            }
            
            // Trigger router kick (Disconnects active session via MikroTik API)
            SyncToRouterJob::dispatch($customer, null, 'disconnect', $customer->tenant_id)
                ->onQueue('network');
                
            Log::info("Deprovisioned/Suspended access for MAC: {$customer->mac_address}");
        });
    }
}