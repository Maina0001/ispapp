<?php

namespace Modules\Customer\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;

/**
 * Fired when a customer's service is officially set to suspended status.
 */
class CustomerSuspended
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(
        public Customer $customer,
        public string $reason // e.g., 'non-payment', 'manual'
    ) {
        $this->tenant_id = $customer->tenant_id;
    }
}