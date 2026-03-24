<?php

namespace Modules\Customer\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Fired when a customer is removed from the system.
 */
class CustomerDeleted
{
    use SerializesModels;

    /**
     * We use IDs here because the model may no longer exist in the DB
     * or is about to be wiped.
     */
    public function __construct(
        public int $customerId,
        public ?int $tenant_id = null
    ) {}
}