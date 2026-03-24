<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Network\Models\RadiusAccount;

/**
 * Fired when a user exceeds their allocated bandwidth or data volume.
 */
class UsageThresholdExceeded
{
    use SerializesModels;

    public ?int $tenant_id;

    /**
     * @param RadiusAccount $radiusAccount
     * @param float $currentUsage Percentage or GB consumed.
     * @param string $thresholdType (e.g., 'soft_cap', 'hard_cap')
     */
    public function __construct(
        public RadiusAccount $radiusAccount,
        public float $currentUsage,
        public string $thresholdType = '80_percent'
    ) {
        $this->tenant_id = $radiusAccount->tenant_id;
    }
}