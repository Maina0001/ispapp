<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;
use Modules\Billing\Models\Subscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use illuminate\Contracts\Events\Dispatcher;
use illuminate\Contracts\Events\ShouldDispatchAfterCommit;

/**
 * Represents the completion of the end-to-end technical onboarding.
 */
class ServiceProvisioned implements ShouldDispatchAfterCommit
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(
        public Customer $customer,
        public Subscription $subscription
    ) {
        $this->tenant_id = $customer->tenant_id;
    }
}