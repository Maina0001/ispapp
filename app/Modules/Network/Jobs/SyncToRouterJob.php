<?php

namespace App\Modules\Network\Jobs;

use App\Core\Jobs\BaseJob;
use App\Modules\Customer\Models\Customer;
use App\Modules\Network\Models\NAS; // The MikroTik Router model
use App\Modules\Network\Services\MikroTikAdapter;
use Illuminate\Support\Facades\Log;

class SyncToRouterJob extends BaseJob
{
    /**
     * @param string $action 'provision'|'disconnect'|'update'
     */
    public function __construct(
        public Customer $customer,
        public $plan,
        public string $action = 'provision',
        ?int $tenant_id = null
    ) {
        parent::__construct($tenant_id);
    }

    public function handle(MikroTikAdapter $adapter): void
    {
        // 1. Identify which router the customer is connected to
        // In a single-router setup, we grab the default NAS. In multi-NAS, we use customer->nas_id
        $nas = NAS::where('is_active', true)->first();

        if (!$nas) {
            Log::error("No active NAS found to sync MAC: {$this->customer->mac_address}");
            return;
        }

        try {
            // 2. Execute the action via the API Adapter
            match ($this->action) {
                'provision', 'update' => $this->syncActiveSession($adapter, $nas),
                'disconnect' => $this->terminateSession($adapter, $nas),
                default => Log::warning("Unknown router sync action: {$this->action}")
            };

        } catch (\Exception $e) {
            Log::error("Router Sync Failed: " . $e->getMessage());
            // We allow the job to fail and retry if the router is temporarily unreachable
            throw $e; 
        }
    }

    protected function syncActiveSession(MikroTikAdapter $adapter, NAS $nas): void
    {
        // Find user in /ip/hotspot/active and update their speed limits
        $adapter->connect($nas->ip_address, $nas->api_username, $nas->api_password);
        $adapter->updateHotspotUserSpeed($this->customer->mac_address, $this->plan);
    }

    protected function terminateSession(MikroTikAdapter $adapter, NAS $nas): void
    {
        // Kick the user off the router (Forces re-authentication)
        $adapter->connect($nas->ip_address, $nas->api_username, $nas->api_password);
        $adapter->removeHotspotActiveSession($this->customer->mac_address);
    }
}