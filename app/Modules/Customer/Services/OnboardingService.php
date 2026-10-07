<?php

namespace Modules\Customer\Services;

use App\Core\Abstract\BaseService;
use App\Core\Context\TenantContext;
use Modules\Billing\Events\SubscriptionCreated;
use Modules\Billing\Models\Subscription;
use Modules\Customer\Events\CustomerOnboarded;
use Modules\Customer\Models\Customer;
use Modules\Network\Models\ServicePlan;
use Modules\Network\Services\ProvisioningService;

class OnboardingService extends BaseService
{
    public function __construct(
        protected ProvisioningService $provisioningService,
        protected TenantContext $tenantContext,
    ) {}

    /**
     * Entry point for Hotspot "Lazy" Onboarding.
     * Triggered when a device hits the portal.
     */
    public function onboardByMac(string $macAddress): Customer
    {
        return Customer::firstOrCreate(
            ['mac_address' => $macAddress],
            [
                'tenant_id' => $this->tenantContext->getTenantId(),
                'status'    => 'lead',
                'name'      => 'Guest ' . substr($macAddress, -8),
            ]
        );
    }

    /**
     * Convert a 'lead' to 'active'.
     *
     * Does NOT provision directly. It creates the Subscription and fires
     * SubscriptionCreated, which triggers the standard event chain:
     *   SubscriptionCreated → ProvisionNetworkAccess → ProvisioningService.
     */
    public function activateCustomer(Customer $customer, ServicePlan $plan): void
    {
        $this->transactional(function () use ($customer, $plan) {
            $customer->update(['status' => 'active']);

            $subscription = $this->createSubscription($customer, $plan);

            event(new SubscriptionCreated($subscription));
            event(new CustomerOnboarded($customer, $subscription));
        });
    }

    /**
     * Renewal-safe: extends expiry if there's an active subscription;
     * creates a new one otherwise.
     */
    protected function createSubscription(Customer $customer, ServicePlan $plan): Subscription
    {
        $existing = Subscription::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();

        $startsAt  = $existing?->starts_at ?? now();
        $expiresAt = $existing
            ? $existing->expires_at->copy()->addMinutes($plan->duration_minutes)
            : now()->addMinutes($plan->duration_minutes);

        return Subscription::updateOrCreate(
            ['customer_id' => $customer->id, 'status' => 'active'],
            [
                'tenant_id'  => $customer->tenant_id,
                'plan_id'    => $plan->id,
                'starts_at'  => $startsAt,
                'expires_at' => $expiresAt,
            ]
        );
    }
}