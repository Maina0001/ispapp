<?php

namespace Modules\Customer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Customer\Events\CustomerRegistered;
use Modules\Network\Services\RadiusManager;
use Throwable;

class SyncCustomerToRadius implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'network';
    public int $tries   = 3;

    public function backoff(): array
    {
        return [15, 60, 180];
    }

    public function __construct(
        protected RadiusManager $radius,
    ) {}

    public function handle(CustomerRegistered $event): void
    {
        $customer = $event->customer;

        if (! $customer->mac_address) {
            Log::warning('SyncCustomerToRadius: customer has no MAC address; skipping.', [
                'customer_id' => $customer->id,
            ]);
            return;
        }

        try {
            // RadiusManager::createRadiusAccount(string $identity, string $password, int $tenantId)
            $this->radius->createRadiusAccount(
                $customer->mac_address,
                $customer->mac_address, // MAC-as-password
                $customer->tenant_id,
            );

            Log::info('SyncCustomerToRadius: RADIUS account created.', [
                'customer_id' => $customer->id,
                'identity'    => $customer->mac_address,
            ]);
        } catch (Throwable $e) {
            Log::error('SyncCustomerToRadius failed.', [
                'customer_id' => $customer->id,
                'identity'    => $customer->mac_address,
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}