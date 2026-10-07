<?php

namespace Modules\Customer\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Subscription;
use Modules\Customer\Models\Customer;

class CustomerOnboarded implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(
        public Customer $customer,
        public Subscription $subscription,
    ) {
        $this->tenant_id = $customer->tenant_id;
    }
}