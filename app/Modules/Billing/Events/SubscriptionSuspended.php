<?php

namespace Modules\Billing\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Billing\Models\Subscription;

/**
 * Indicates that a subscription service has been halted.
 */
class SubscriptionSuspended
{
    use SerializesModels;

    public ?int $tenant_id;

    /**
     * @param Subscription $subscription
     * @param string $reason ('overdue', 'manual', 'expired')
     */
    public function __construct(
        public Subscription $subscription,
        public string $reason = 'overdue'
    ) {
        $this->tenant_id = $subscription->tenant_id;
    }
}