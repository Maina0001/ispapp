<?php

namespace Modules\Customer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Modules\Customer\Models\Customer;

/**
 * Represents the fact that a new customer has been persisted.
 */
class CustomerRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Customer $customer)
    {
        // Future Multi-tenant: Ensure the tenant context is captured for async listeners
        $this->tenant_id = $customer->tenant_id;
    }
}