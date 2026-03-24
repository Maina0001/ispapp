<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;

/**
 * Triggered when a network service is permanently decommissioned.
 */
class ServiceDeprovisioned
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(
        public Customer $customer,
        public string $terminationReason = 'churn'
    ) {
        $this->tenant_id = $customer->tenant_id;
    }
}