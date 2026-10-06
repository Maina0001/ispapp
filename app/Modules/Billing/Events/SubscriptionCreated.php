<?php

namespace Modules\Billing\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Subscription;

class SubscriptionCreated implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public ?int $tenant_id;

    public function __construct(public Subscription $subscription)
    {
        $this->tenant_id = $subscription->tenant_id;
    }
}