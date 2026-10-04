<?php

namespace App\Modules\Network\Drivers;

use App\Modules\Customer\Models\Customer;
use App\Modules\Network\Interfaces\NetworkDriverInterface;
use App\Modules\Network\Services\MikroTikAdapter;
use App\Modules\Network\Services\RadiusManager;
use Illuminate\Support\Facades\Log;

class RadiusMikroTikDriver implements NetworkDriverInterface
{
    public function __construct(
        protected RadiusManager $radius,
        protected MikroTikAdapter $mikrotik,
    ) {}

    public function authenticateUser(string $identity, array $options = []): bool
    {
        $customer = Customer::where('mac_address', $identity)->first();

        if (! $customer) {
            Log::error("Driver: no customer found for MAC {$identity}");
            return false;
        }

        // RadiusManager fires RadiusAccountCreated internally.
        $this->radius->createRadiusAccount($customer, $options['password'] ?? $identity);

        return true;
    }

    public function suspendUser(string $identity): bool
    {
        $customer = Customer::where('mac_address', $identity)->first();

        if (! $customer || ! $customer->radiusAccount) {
            return false;
        }

        // RadiusManager fires RadiusAccountSuspended internally.
        $this->radius->suspendRadiusAccount($customer->radiusAccount, 'billing');

        return true;
    }

    public function resumeUser(string $identity): bool
    {
        $customer = Customer::where('mac_address', $identity)->first();

        if (! $customer || ! $customer->radiusAccount) {
            return false;
        }

        // RadiusManager fires RadiusAccountResumed internally.
        $this->radius->resumeRadiusAccount($customer->radiusAccount, 'payment_received');

        return true;
    }

    public function removeUser(string $identity): bool
    {
        // For now, permanent removal = suspension. If you later support MAC
        // reuse, delete the RADIUS row here instead.
        return $this->suspendUser($identity);
    }

    public function updateBandwidth(string $identity, string $profileName): bool
    {
        $this->connectRouter();
        $this->mikrotik->updateHotspotUserSpeed($identity, $profileName);
        return true;
    }

    public function getActiveSession(string $identity): ?array
    {
        $this->connectRouter();
        // You'd add a MikroTikAdapter method for this if not already present.
        return $this->mikrotik->getActiveSession($identity);
    }

    public function disconnectUser(string $identity): void
    {
        $this->connectRouter();
        $this->mikrotik->removeHotspotActiveSession($identity);
    }

    protected function connectRouter(): void
    {
        $this->mikrotik->connect(
            config('network.router.ip'),
            config('network.router.user'),
            config('network.router.pass'),
        );
    }
}