<?php

namespace Modules\Customer\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;

/**
 * Fired when customer profile attributes are modified.
 */
class CustomerUpdated
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(
        public Customer $customer,
        public array $changedAttributes
    ) {
        $this->tenant_id = $customer->tenant_id;
    }
}