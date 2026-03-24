<?php

namespace Modules\Billing\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Subscription;

/**
 * Triggered when a suspended service is restored to active status.
 */
class SubscriptionReactivated
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Subscription $subscription)
    {
        $this->tenant_id = $subscription->tenant_id;
    }
}