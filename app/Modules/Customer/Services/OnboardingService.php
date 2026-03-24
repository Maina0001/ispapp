<?php

namespace App\Modules\Customer\Services;

use App\Core\Abstract\BaseService;
use App\Modules\Customer\Models\Customer;
use App\Modules\Billing\Models\Subscription;
use App\Modules\Network\Services\ProvisioningService;
use App\Modules\Customer\Events\CustomerOnboarded;

class OnboardingService extends BaseService
{
    public function __construct(
        protected ProvisioningService $provisioningService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Entry point for a new installation.
     */
    public function onboardCustomer(Customer $customer, array $subscriptionData): void
    {
        $this->transactional(function () use ($customer, $subscriptionData) {
            // 1. Update customer status
            $customer->update(['status' => 'active']);

            // 2. Create the initial subscription
            $subscription = $this->createSubscription($customer, $subscriptionData);

            // 3. Provision Network Access (Radius/MikroTik)
            $this->provisionInitialNetworkAccess($customer, $subscription);

            // 4. Send Welcome Comms
            $this->notificationService->sendWelcomeNotification($customer);

            // 5. Fire Event for secondary modules (e.g., Reporting)
            event(new CustomerOnboarded($customer, $subscription));
        });
    }

    public function createSubscription(Customer $customer, array $data): Subscription
    {
        return Subscription::create([
            'customer_id' => $customer->id,
            'service_plan_id' => $data['plan_id'],
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addMonth(), // Standard 30-day cycle
        ]);
    }

    public function provisionInitialNetworkAccess(Customer $customer, Subscription $subscription): void
    {
        // Delegates to Network Module to handle Radius credentials and NAS sync
        $this->provisioningService->initializeAccount($customer, $subscription);
    }
}