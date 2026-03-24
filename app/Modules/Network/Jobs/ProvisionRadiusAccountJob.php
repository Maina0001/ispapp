<?php

namespace Modules\Network\Jobs;

use App\Core\Jobs\BaseJob;
use Modules\Network\Models\RadiusAccount;
use Modules\Network\Models\BandwidthProfile;

class ProvisionRadiusAccountJob extends BaseJob
{
    public function __construct(
        protected string $mac, 
        protected int $planId, 
        $tenantId
    ) {
        parent::__construct($tenantId);
    }

    public function handle(): void
    {
        $plan = BandwidthProfile::findOrFail($this->planId);

        // 1. Clear any old sessions for this MAC
        RadiusAccount::where('username', $this->mac)->delete();

        // 2. Insert new Cleartext-Password (the MAC itself is usually the password in hotspots)
        RadiusAccount::create([
            'username' => $this->mac,
            'attribute' => 'Cleartext-Password',
            'op' => ':=',
            'value' => $this->mac,
            'tenant_id' => $this->tenant_id
        ]);

        // 3. Set Bandwidth Limits (MikroTik-Rate-Limit attribute)
        RadiusAccount::create([
            'username' => $this->mac,
            'attribute' => 'MikroTik-Rate-Limit',
            'op' => ':=',
            'value' => "{$plan->upload_limit}k/{$plan->download_limit}k",
            'tenant_id' => $this->tenant_id
        ]);
    }
}