<?php

namespace App\Modules\Customer\Services;

use App\Core\Abstract\BaseService;
use Modules\Customer\Models\Customer;
use Modules\Billing\Models\Subscription;
use Modules\Network\Models\ServicePlan;
use Modules\Network\Services\ProvisioningService;
use Modules\Customer\Events\CustomerOnboarded;
use App\Core\Context\TenantContext;

class OnboardingService extends BaseService
{
    public function __construct(
        protected ProvisioningService $provisioningService,
        protected TenantContext $tenantContext
        // We removed NotificationService for now to keep it lean, add back if needed
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
                'status'    => 'lead', // Initial state: "Just Browsing"
                'username'  => 'guest_' . str_replace(':', '', $macAddress),
                'password'  => bcrypt($macAddress),
            ]
        );
    }

    /**
     * Triggered after a successful M-Pesa payment.
     * Converts a 'lead' to 'active' and sets up the timer.
     */
    public function activateCustomer(Customer $customer, ServicePlan $plan): void
    {
        $this->transactional(function () use ($customer, $plan) {
            
            // 1. Update status to active
            $customer->update(['status' => 'active']);

            // 2. Create/Update Subscription using Plan duration
            $subscription = $this->createSubscription($customer, $plan);

            // 3. Provision Network Access via the Network Module Driver
            // We pass the plan so the driver knows the bandwidth limit (e.g. 5M/5M)
            $this->provisioningService->provisionCustomerService($customer, $plan);

            // 4. Fire Event for secondary modules
            event(new CustomerOnboarded($customer, $subscription));
        });
    }

    protected function createSubscription(Customer $customer, ServicePlan $plan): Subscription
    {
        return Subscription::updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'tenant_id'      => $customer->tenant_id,
                'plan_id'        => $plan->id,
                'status'         => 'active',
                'starts_at'      => now(),
                // Logic: Add minutes from the ServicePlan row
                'expires_at'     => now()->addMinutes($plan->duration_minutes),
            ]
        );
    }
    /**
 * Check if the customer can use a trial based on current time and history.
 */
public function checkTrialEligibility(Customer $customer): bool
{
    $now = now();
    $start = now()->setTime(7, 0);
    $end = now()->setTime(9, 0);

    // 1. Check Time Window
    if (!$now->between($start, $end)) return false;

    // 2. Check if they used it today
    return !$customer->subscriptions()
        ->where('is_trial', true)
        ->where('created_at', '>=', now()->startOfDay())
        ->exists();
}

/**
 * Activate a temporary 30-minute access window.
 */
public function activateTrial(Customer $customer): void
{
    $this->transactional(function () use ($customer) {
        $subscription = Subscription::create([
            'customer_id' => $customer->id,
            'tenant_id'   => $customer->tenant_id,
            'status'      => 'active',
            'is_trial'    => true,
            'expires_at'  => now()->addMinutes(30),
        ]);

        // Trigger network access with a 'trial' speed profile
        $this->provisioningService->provisionTrialAccess($customer);
    });
}
}