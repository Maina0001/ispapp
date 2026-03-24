<?php

namespace Modules\Network\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Customer\Models\Customer;
use Modules\Billing\Models\Subscription;

/**
 * Represents the completion of the end-to-end technical onboarding.
 */
class ServiceProvisioned
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