<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Network\Models\RadiusAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use illuminate\Contracts\Events\Dispatcher;
use illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Modules\Billing\Models\Subscription;

/**
 * Triggered when a RADIUS account is flagged as inactive or restricted.
 */
class RadiusAccountSuspended implements ShouldDispatchAfterCommit
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