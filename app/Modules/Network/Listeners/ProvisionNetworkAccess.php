<?php

namespace Modules\Network\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Billing\Events\SubscriptionCreated;
use Modules\Network\Services\ProvisioningService;

class ProvisionNetworkAccess implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'network';

    public int $tries = 5;

    public function backoff(): array
    {
        return [15, 60, 180, 600, 1800];
    }

    public function __construct(
        protected ProvisioningService $provisioningService
    ) {}

    public function handle(SubscriptionCreated $event): void
    {
    
        $subscription = $event->subscription;
        $customer     = $subscription->customer;
        $plan         = $subscription->plan;

        // Guard: subscription must be complete enough to provision
        if (! $customer || ! $plan) {
            Log::error('ProvisionNetworkAccess: subscription is missing customer or plan.', [
                'subscription_id' => $subscription->id,
                'customer_id'     => $subscription->customer_id,
                'plan_id'         => $subscription->plan_id,
            ]);
            return;
        }

        // Optional: set tenancy context if you use a tenancy package
        if ($subscription->tenant_id) {
            // tenancy()->initialize($subscription->tenant_id);
        }

        try {
            $this->provisioningService->provisionCustomerService($subscription);

            Log::info('ProvisionNetworkAccess: customer provisioned.', [
                'tenant_id'       => $subscription->tenant_id,
                'subscription_id' => $subscription->id,
                'customer_id'     => $customer->id,
                'plan_id'         => $plan->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('ProvisionNetworkAccess: provisioning failed.', [
                'tenant_id'       => $subscription->tenant_id,
                'subscription_id' => $subscription->id,
                'customer_id'     => $customer->id,
                'plan_id'         => $plan->id,
                'attempt'         => $this->attempts(),
                'error'           => $e->getMessage(),
            ]);

            // Rethrow so the queue retries according to $tries and backoff()
            throw $e;
        }
    }

    public function failed(SubscriptionCreated $event, \Throwable $e): void
    {
        Log::critical('ProvisionNetworkAccess: permanently failed after retries.', [
            'subscription_id' => $event->subscription->id,
            'customer_id'     => $event->subscription->customer_id,
            'plan_id'         => $event->subscription->plan_id,
            'error'           => $e->getMessage(),
        ]);

        // Optional: mark the subscription as failed so billing can react
        // $event->subscription->update(['status' => 'provisioning_failed']);

        // Optional: notify admins / fire a NetworkProvisioningFailed event
        // event(new \Modules\Network\Events\ProvisioningFailed($event->subscription, $e));
    }
}