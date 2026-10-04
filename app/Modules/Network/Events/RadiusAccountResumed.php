<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Network\Models\RadiusAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use illuminate\Contracts\Events\Dispatcher;
use illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Modules\Billing\Models\Subscription;

/**
 * Indicates that a suspended account has been cleared for network access.
 */
class RadiusAccountResumed implements ShouldDispatchAfterCommit
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public RadiusAccount $radiusAccount)
    {
        $this->tenant_id = $radiusAccount->tenant_id;
    }
}