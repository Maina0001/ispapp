<?php

namespace App\Modules\Network\Services;

use App\Modules\Network\Events\RadiusAccountCreated;
use App\Modules\Network\Events\RadiusAccountResumed;
use App\Modules\Network\Events\RadiusAccountSuspended;
use App\Modules\Network\Models\RadiusAccount;
use Illuminate\Support\Facades\Log;
use Throwable;

class RadiusManager
{
    public function __construct(
        protected FreeRadiusAdapter $adapter,
        protected MikroTikAdapter $mikrotik,
    ) {}

    /**
     * Create or refresh a RADIUS account for an identity (MAC address).
     *
     * Idempotent: safe to call multiple times for the same identity.
     * The adapter's updateOrCreate ensures no duplicate rows.
     */
    public function createRadiusAccount(string $identity, string $password, int $tenantId): RadiusAccount
    {
        $account = $this->adapter->createUser($identity, $password, $tenantId);

        event(new RadiusAccountCreated($account));

        return $account;
    }

    /**
     * Suspend a customer:
     *   1. Disable the RADIUS account so they cannot re-authenticate.
     *   2. Kick the current active session so the change takes effect NOW.
     */
    public function suspendRadiusAccount(RadiusAccount $account, string $reason = 'billing'): void
    {
        // 1. Disable first — the customer must not be able to reconnect.
        $this->adapter->updateUser($account, [
            'is_active' => false,
        ]);

        // 2. Then kick. If this fails, RADIUS is still disabled; the
        //    customer is locked out at next reconnect. The kick is best-effort.
        $this->kickActiveSession($account->username);

        event(new RadiusAccountSuspended($account, $reason));
    }

    /**
     * Resume a customer. No kick needed — they are already offline
     * (they were suspended). They will reconnect on their own.
     */
    public function resumeRadiusAccount(RadiusAccount $account, string $reason = 'payment_received'): void
    {
        $this->adapter->updateUser($account, [
            'is_active' => true,
        ]);

        event(new RadiusAccountResumed($account, $reason));
    }

    /**
     * Disconnect the customer's active session on the router.
     * Swallows-and-logs infrastructure errors so the caller can decide
     * whether to retry; throws nothing if the customer isn't connected.
     */
    protected function kickActiveSession(string $identity): void
    {
        try {
            $this->mikrotik->connect(
                config('network.router.ip'),
                config('network.router.user'),
                config('network.router.pass'),
            );

            $this->mikrotik->removeHotspotActiveSession($identity);
        } catch (Throwable $e) {
            // Log but don't rethrow: RADIUS is already disabled, the
            // customer is effectively locked out. The kick is best-effort.
            Log::warning("Failed to kick active session for {$identity}: {$e->getMessage()}", [
                'identity' => $identity,
            ]);
        }
    }
}