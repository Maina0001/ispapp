<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Network\Models\RadiusAccount;

/**
 * Indicates that a suspended account has been cleared for network access.
 */
class RadiusAccountResumed
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public RadiusAccount $radiusAccount)
    {
        $this->tenant_id = $radiusAccount->tenant_id;
    }
}