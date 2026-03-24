<?php

namespace Modules\Billing\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Subscription;

/**
 * Fired when a new service subscription record is persisted.
 */
class SubscriptionCreated
{
    use SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Subscription $subscription)
    {
        $this->tenant_id = $subscription->tenant_id;
    }
}