<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;
use Illuminate\Contracts\Queue\ShouldQueue;
use illuminate\Contracts\Events\Dispatcher;
use illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Modules\Billing\Models\Subscription;

/**
 * Triggered when a network service is permanently decommissioned.
 */
class ServiceDeprovisioned implements ShouldDispatchAfterCommit
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