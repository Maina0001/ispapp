<?php

namespace Modules\Customer\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;

/**
 * Fired when a customer is moved back to 'active' status.
 */
class CustomerReactivated
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Customer $customer)
    {
        $this->tenant_id = $customer->tenant_id;
    }
}