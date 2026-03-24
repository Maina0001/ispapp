<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\Network\Models\RadiusAccount;

/**
 * Indicates that a new set of RADIUS credentials has been generated.
 */
class RadiusAccountCreated
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