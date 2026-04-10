<?php

namespace App\Modules\Network\Services;

use App\Core\Abstract\BaseService;
use App\Modules\Network\Interfaces\NetworkDriverInterface;
use App\Modules\Customer\Models\Customer;

class ProvisioningService extends BaseService
{
    // We type-hint the INTERFACE, not the specific class
    public function __construct(
        protected NetworkDriverInterface $driver 
    ) {}

    public function provisionCustomerService(Customer $customer, $plan): void
    {
        $this->transactional(function () use ($customer, $plan) {
            
            // The service doesn't care if it's Radius, MikroTik API, or Huawei
            $this->driver->provision($customer, $plan);

            // Dispatching events and logging remains in the Service
            event(new ServiceProvisioned($customer, $plan));
            
            $this->logActivity("Provisioning triggered for MAC: {$customer->mac_address}");
        });
    }
}