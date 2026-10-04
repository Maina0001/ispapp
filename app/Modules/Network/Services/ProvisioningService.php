<?php

namespace Modules\Network\Services;

use App\Core\Abstract\BaseService;
use Illuminate\Support\Facades\Log;
use Modules\Billing\Models\Subscription;
use Modules\Customer\Models\Customer;
use Modules\Network\Events\ServiceDeprovisioned;
use Modules\Network\Events\ServiceProvisioned;
use Modules\Network\Interfaces\NetworkDriverInterface;
use Throwable;

class ProvisioningService extends BaseService
{
    public function __construct(
        protected NetworkDriverInterface $driver
    ) {}

    /**
     * End-to-end onboarding for a subscription.
     *
     * The driver handles the technical plumbing (RADIUS, MikroTik, …).
     * This service owns the ordering and the outcome event.
     */
    public function provisionCustomerService(Subscription $subscription): void
    {
        $customer = $subscription->customer;
        $plan     = $subscription->plan;

        if (! $customer || ! $plan) {
            Log::error('ProvisioningService: subscription missing customer or plan.', [
                'subscription_id' => $subscription->id,
            ]);
            return;
        }

        if (! $customer->mac_address) {
            Log::error("ProvisioningService: customer {$customer->id} has no MAC address.");
            return;
        }

        $identity = $customer->mac_address;

        try {
            $this->transactional(function () use ($customer, $plan, $subscription, $identity) {
                // 1. Ensure RADIUS credentials exist. Idempotent.
                $this->driver->authenticateUser($identity, [
                    'password'  => $identity, // MAC-as-password
                    'tenant_id' => $customer->tenant_id,
                ]);

                // 2. Apply the plan's bandwidth profile.
                $this->driver->updateBandwidth(
                    $identity,
                    $plan->router_profile_name ?? 'default'
                );

                // 3. Kick any stale session so the new rules take effect now.
                $this->driver->disconnectUser($identity);

                // 4. Outcome event — deferred to commit.
                event(new ServiceProvisioned($customer, $subscription));

                $this->logActivity(
                    "Provisioned customer {$customer->id} (MAC: {$identity}, plan: {$plan->id})"
                );
            });
        } catch (Throwable $e) {
            Log::error('ProvisioningService: provisioning failed.', [
                'subscription_id' => $subscription->id,
                'customer_id'     => $customer->id,
                'plan_id'         => $plan->id,
                'error'           => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Deprovision: disable RADIUS, kick the session, fire the outcome event.
     */
    public function deprovisionCustomerService(Customer $customer, string $reason = 'churn'): void
    {
        if (! $customer->mac_address) {
            Log::error("ProvisioningService: customer {$customer->id} has no MAC address.");
            return;
        }

        $identity = $customer->mac_address;

        try {
            $this->transactional(function () use ($customer, $reason, $identity) {
                // 1. Disable RADIUS so the customer can't re-auth.
                $this->driver->suspendUser($identity);

                // 2. Kick them off right now.
                $this->driver->disconnectUser($identity);

                // 3. Outcome event.
                event(new ServiceDeprovisioned($customer, $reason));

                $this->logActivity(
                    "Deprovisioned customer {$customer->id} (MAC: {$identity}, reason: {$reason})"
                );
            });
        } catch (Throwable $e) {
            Log::error('ProvisioningService: deprovisioning failed.', [
                'customer_id' => $customer->id,
                'reason'      => $reason,
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Restore a previously suspended customer.
     * No bandwidth re-application — the subscription is intact.
     */
    public function restoreCustomerService(Customer $customer): void
    {
        if (! $customer->mac_address) {
            Log::error("ProvisioningService: customer {$customer->id} has no MAC address.");
            return;
        }

        $identity = $customer->mac_address;

        try {
            $this->transactional(function () use ($customer, $identity) {
                $this->driver->resumeUser($identity);
                $this->logActivity("Restored customer {$customer->id} (MAC: {$identity})");
            });
        } catch (Throwable $e) {
            Log::error('ProvisioningService: restore failed.', [
                'customer_id' => $customer->id,
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}