<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Network\Models\RadiusAccount;

/**
 * Triggered when a RADIUS account is flagged as inactive or restricted.
 */
class RadiusAccountSuspended
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(
        public RadiusAccount $radiusAccount,
        public string $reason = 'billing'
    ) {
        $this->tenant_id = $radiusAccount->tenant_id;
    }
}