<?php

namespace Modules\Billing\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Subscription;

/**
 * Fired when a new service subscription record is persisted.
 *
 * Dispatched by ProvisionInternetAccess after creating or extending a
 * subscription. ShouldDispatchAfterCommit ensures ProvisionNetworkAccess
 * only runs once the subscription is committed to the database — so the
 * listener never provisions hardware for a subscription that might be
 * rolled back.
 */
class SubscriptionCreated implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Subscription $subscription)
    {
        $this->tenant_id = $subscription->tenant_id;
    }
}