<?php

namespace Modules\Network\Listeners;

use Modules\Customer\Events\CustomerSuspended;
use Modules\Network\Services\ProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;

class DisableNetworkAccess implements ShouldQueue
{
    public string $queue = 'network';

    public function __construct(
        protected ProvisioningService $provisioningService
    ) {}

    public function handle(CustomerSuspended $event): void
    {
        // Orchestrate: Disconnect active sessions and update RADIUS status
        $this->provisioningService->deprovisionCustomerService($event->customer);
    }
}