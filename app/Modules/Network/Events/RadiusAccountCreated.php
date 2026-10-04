<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Network\Models\RadiusAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Events\Dispatcher;
use illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Modules\Billing\Models\Subscription;

/**
 * Indicates that a new set of RADIUS credentials has been generated.
 */
class RadiusAccountCreated implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    /**
     * @param RadiusAccount $radiusAccount The newly created account model.
     */
    public function __construct(public RadiusAccount $radiusAccount)
    {
        $this->tenant_id = $radiusAccount->tenant_id;
    }
}